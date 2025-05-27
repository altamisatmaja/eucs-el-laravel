<?php

namespace App\Http\Controllers;

use App\Imports\RecordsImport;
use App\Imports\RecordValuesImport;
use App\Models\Record;
use App\Models\RecordValue;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $reference = $request->query('references');

        if ($reference) {
            $query = RecordValue::orderBy('record_id');

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
            session(['recordIds' => $recordIds]);


            return view('dashboard', [
                'recordIds' => $recordIds,
                'formattedData' => $formattedData,
                'success' => session('success'),
                'reference' => $reference,
                'referenceData' => $referenceData,
                'sessionRecordId' => $recordIds,
            ]);
        }

        return view('dashboard');
    }

    public function clear()
    {
        session()->forget(['existingRecordId', 'recordIds']);

        return redirect()->route('dashboard')->with('success', 'Berhasil dibersihkan');
    }
}
