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
        $typeLower = strtolower($type ?? 'x'); 
        
        $hasAnyData = RecordValue::exists();
        
        $query = RecordValue::where('variable', 'like', $typeLower . '%')
            ->orderBy('record_id');
        
        if ($reference) {
            $query->where('record_id', $reference);
        }

        $values = $query->get();

        
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

        $fileName = $file->getClientOriginalName();
        $fileExtension = strtolower($file->getClientOriginalExtension());

        
        $allowedExtensions = ['csv', 'xls', 'xlsx'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            return back()->with('error', 'Format file tidak didukung. Silakan upload file CSV atau Excel (.csv, .xls, .xlsx)');
        }

        try {
            $record = Record::create([
                'user_id' => auth()->id(),
                'name' => pathinfo($fileName, PATHINFO_FILENAME) . ' - ' . now()->format('Y-m-d H:i:s'),
            ]);

            Excel::import(new RecordValuesImport($record->id), $request->file('file'));
            session(['existingRecordId' => $record->id]);

            return redirect()
                ->route('dashboard', ['references' => $record->id])
                ->with('success', 'File "' . $fileName . '" berhasil diproses!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file "' . $fileName . '": ' . $e->getMessage());
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
        
        $firstRecord = RecordValue::findOrFail($id);

        
        $respondentData = $this->getRespondentRecords($firstRecord);

        
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
        
        $firstRecord = RecordValue::findOrFail($id);

        
        $respondentData = $this->getRespondentRecords($firstRecord);

        
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
        
        $allIds = RecordValue::where('record_id', $record->record_id)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        
        $position = $this->getRespondentPosition($record);
        $varCount = $this->countVariablesForRecord($record->record_id);

        
        $ids = array_slice($allIds, $position, $varCount);

        
        return RecordValue::whereIn('id', $ids)->get();
    }

    public function destroyRespondent($id)
    {
        $firstRecord = RecordValue::findOrFail($id);

        
        $position = $this->getRespondentPosition($firstRecord);
        $varCount = $this->countVariablesForRecord($firstRecord->record_id);

        
        $allIds = RecordValue::where('record_id', $firstRecord->record_id)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        
        $idsToDelete = array_slice($allIds, $position, $varCount);

        
        RecordValue::whereIn('id', $idsToDelete)->delete();

        return redirect()->back()->with('success', 'Data responden berhasil dihapus');
    }

    private function getRespondentPosition($record)
    {
        
        return RecordValue::where('record_id', $record->record_id)
            ->where('variable', $record->variable)
            ->where('id', '<=', $record->id)
            ->count() - 1;
    }

    private function countVariablesForRecord($recordId)
    {
        
        return RecordValue::where('record_id', $recordId)
            ->distinct('variable')
            ->count('variable');
    }

    
    
    
}
