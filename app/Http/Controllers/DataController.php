<?php

namespace App\Http\Controllers;

use App\Imports\RecordsImport;
use App\Imports\RecordValuesImport;
use App\Models\Record;
use App\Models\RecordValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\LengthAwarePaginator;

class DataController extends Controller
{
    public function index($type = null, Request $request)
    {
        $reference = $request->query('references');
        $perPage = 10;
        $typeLower = strtolower($type ?? 'x');

        $hasAnyData = RecordValue::exists();

        // Jika tidak ada reference, tampilkan daftar record_ids
        if (!$reference) {
            $recordIds = RecordValue::distinct('record_id')->pluck('record_id')->toArray();

            return view('data.index', [
                'recordIds' => $recordIds,
                'formattedData' => [],
                'type' => strtoupper($type ?? 'X'),
                'success' => session('success'),
                'reference' => $reference,
                'referenceData' => [],
                'hasAnyData' => $hasAnyData,
                'hasCurrentData' => false
            ]);
        }

        // Jika ada reference, ambil data dengan pagination yang benar
        $query = RecordValue::where('record_id', $reference)
            ->where('variable', 'like', $typeLower . '%')
            ->orderBy('variable')
            ->orderBy('id'); // Pastikan ada kolom untuk mengurutkan responden

        $totalRespondents = $query->count();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Ambil semua data untuk record_id ini
        $allValues = $query->get()->groupBy('variable');

        // Format data dengan struktur yang diharapkan
        $formattedData = [];
        $referenceData = [];

        foreach ($allValues as $variable => $values) {
            foreach ($values as $index => $value) {
                $formattedData[$reference][$variable][$index] = $value->value;
            }
        }

        // Buat paginator untuk responden
        $firstVar = array_key_first($formattedData[$reference] ?? []);
        $respondentCount = $firstVar ? count($formattedData[$reference][$firstVar]) : 0;

        // Potong data responden berdasarkan halaman
        $paginatedData = [];
        $start = ($currentPage - 1) * $perPage;
        $end = $start + $perPage;

        foreach ($formattedData[$reference] as $variable => $respondents) {
            $paginatedData[$variable] = array_slice($respondents, $start, $perPage);
        }

        $values = new LengthAwarePaginator(
            $paginatedData,
            $respondentCount,
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query()
            ]
        );

        return view('data.index', [
            'recordIds' => [$reference],
            'formattedData' => [$reference => $paginatedData],
            'type' => strtoupper($type ?? 'X'),
            'success' => session('success'),
            'reference' => $reference,
            'referenceData' => $referenceData,
            'hasAnyData' => $hasAnyData,
            'hasCurrentData' => !empty($paginatedData),
            'pagination' => $values,
            'values' => $values,
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
