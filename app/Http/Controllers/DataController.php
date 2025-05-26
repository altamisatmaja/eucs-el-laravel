<?php

namespace App\Http\Controllers;

use App\Imports\RecordsImport;
use App\Imports\RecordValuesImport;
use App\Models\Record;
use App\Models\RecordValue;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DataController extends Controller
{
    public function index($type = null, Request $request)
    {
        $reference = $request->query('references');
        $typeLower = strtolower($type ?? 'x'); // 'x' or 'y'

        // Query directly to RecordValue with variable filtering
        $query = RecordValue::where('variable', 'like', $typeLower . '%')
            ->orderBy('variable')
            ->orderBy('record_id');

        // If there's a reference, filter by record_id
        if ($reference) {
            $query->where('record_id', $reference);
        }

        $values = $query->get();

        // Prepare data structure
        $formattedData = [];
        $referenceData = [];

        foreach ($values as $value) {
            $formattedData[$value->record_id][$value->variable][] = $value->value;

            if ($reference && $value->record_id == $reference) {
                $referenceData[$value->variable] = $value->value;
            }
        }

        // Get unique record IDs for display
        $recordIds = array_keys($formattedData);

        return view('data.index', [
            'recordIds' => $recordIds, // Pass record IDs instead of records
            'formattedData' => $formattedData,
            'type' => strtoupper($type ?? 'X'),
            'success' => session('success'),
            'reference' => $reference,
            'referenceData' => $referenceData
        ]);
    }



    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');

        try {
            $record = Record::create([
                'user_id' => auth()->id(),
                'name' => 'Data ' . ' - ' . now()->format('Y-m-d H:i:s'),
            ]);

            Excel::import(new RecordValuesImport($record->id), $request->file('file'));

            return redirect()
                ->route('dashboard', ['references' => $record->id])
                ->with('success', 'File berhasil diproses!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }



    public function edit($id)
    {
        $record = RecordValue::findOrFail($id);
        return view('data.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = RecordValue::findOrFail($id);
        $record->update($request->only('value'));
        return redirect()->back()->with('success', 'Data updated successfully');
    }

    public function destroy($id)
    {
        RecordValue::destroy($id);
        return redirect()->back()->with('success', 'Data deleted successfully');
    }
}
