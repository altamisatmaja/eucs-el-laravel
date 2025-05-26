<?php

namespace App\Http\Controllers;

use App\Imports\RecordsImport;
use App\Imports\RecordValuesImport;
use App\Models\Record;
use App\Models\RecordValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DataController extends Controller
{
    public function index($type = null, Request $request)
    {

        $reference = $request->query('references');
        if (!$reference) {
            return view('data.index', [
                'recordIds' => $recordIds ?? '',
                'formattedData' => $formattedData ?? '',
                'type' => strtoupper($type ?? 'X'),
                'success' => session('success'),
                'reference' => $reference,
                'referenceData' => $referenceData ?? '',
                'hasAnyData' => $hasAnyData ?? '',
                'hasCurrentData' => !empty($formattedData)
            ]);
        }
        $typeLower = strtolower($type ?? 'x'); // 'x' or 'y'

        // Check if there's any data at all in the database
        $hasAnyData = RecordValue::exists();

        // Query directly to RecordValue with variable filtering
        $query = RecordValue::where('variable', 'like', $typeLower . '%')
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

        $recordIds = array_keys($formattedData);

        return view('data.index', [
            'recordIds' => $recordIds,
            'formattedData' => $formattedData,
            'type' => strtoupper($type ?? 'X'),
            'success' => session('success'),
            'reference' => $reference,
            'referenceData' => $referenceData,
            'hasAnyData' => $hasAnyData,
            'hasCurrentData' => !empty($formattedData)
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
            session(['existingRecordId' => $record->id]);

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
        $request->validate([
            'value' => 'required'
        ]);

        $record = RecordValue::findOrFail($id);
        $record->update($request->only('value'));

        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $record = RecordValue::findOrFail($id);
        $record->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function editRespondent($id)
    {
        // Get the first record to identify the respondent
        $firstRecord = RecordValue::findOrFail($id);

        // Get all records for this respondent
        $respondentData = $this->getRespondentRecords($firstRecord);

        // Format data for the form
        $formattedData = [];
        foreach ($respondentData as $record) {
            $formattedData[$record->variable] = $record->value;
        }

        return view('data.edit-respondent', [
            'recordId' => $firstRecord->record_id,
            'respondentPosition' => $this->getRespondentPosition($firstRecord),
            'data' => $formattedData,
            'variables' => array_keys($formattedData),
            'id' => $id
        ]);
    }

    public function updateRespondent(Request $request, $id)
    {
        // Get the first record to identify the respondent
        $firstRecord = RecordValue::findOrFail($id);

        // Get all records for this respondent
        $respondentData = $this->getRespondentRecords($firstRecord);

        // Update each value
        foreach ($respondentData as $record) {
            $variable = $record->variable;
            if ($request->has($variable)) {
                $record->update(['value' => $request->$variable]);
            }
        }

        return redirect()->back()->with('success', 'Data responden berhasil diperbarui');
    }

    private function getRespondentRecords($record)
    {
        // First get all IDs for this record ordered by ID
        $allIds = RecordValue::where('record_id', $record->record_id)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        // Calculate the position and number of variables
        $position = $this->getRespondentPosition($record);
        $varCount = $this->countVariablesForRecord($record->record_id);

        // Get the slice of IDs we need
        $ids = array_slice($allIds, $position, $varCount);

        // Now get the actual records
        return RecordValue::whereIn('id', $ids)->get();
    }

    public function destroyRespondent($id)
    {
        $firstRecord = RecordValue::findOrFail($id);

        // Get the position of this respondent
        $position = $this->getRespondentPosition($firstRecord);
        $varCount = $this->countVariablesForRecord($firstRecord->record_id);

        // Get all IDs for this record ordered by ID
        $allIds = RecordValue::where('record_id', $firstRecord->record_id)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        // Get the slice of IDs we need to delete
        $idsToDelete = array_slice($allIds, $position, $varCount);

        // Delete the records
        RecordValue::whereIn('id', $idsToDelete)->delete();

        return redirect()->back()->with('success', 'Data responden berhasil dihapus');
    }

    private function getRespondentPosition($record)
    {
        // Get the position of this respondent in the sequence
        return RecordValue::where('record_id', $record->record_id)
            ->where('variable', $record->variable)
            ->where('id', '<=', $record->id)
            ->count() - 1;
    }

    private function countVariablesForRecord($recordId)
    {
        // Count how many variables exist for this record
        return RecordValue::where('record_id', $recordId)
            ->distinct('variable')
            ->count('variable');
    }

    // Keep the existing helper methods:
    // getRespondentPosition()
    // countVariablesForRecord()
}
