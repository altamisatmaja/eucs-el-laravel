<?php

namespace App\Http\Controllers;

use App\Imports\RecordsImport;
use App\Imports\RecordValuesImport;
use App\Models\Record;
use App\Models\RecordValue;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $reference = $request->query('references');
        $perPage = 10; // Jumlah item per halaman

        if ($reference) {
            // Ambil semua data untuk record_id yang dipilih
            $allValues = RecordValue::where('record_id', $reference)
                ->orderBy('variable')
                ->orderBy('id')
                ->get();

            // Format data seperti sebelumnya
            $formattedData = [];
            $referenceData = [];

            foreach ($allValues as $value) {
                $formattedData[$value->variable][] = $value->value;

                if ($value->record_id == $reference) {
                    $referenceData[$value->variable] = $value->value;
                }
            }

            // Hitung jumlah responden (ambil dari variable pertama)
            $firstVar = array_key_first($formattedData);
            $totalRespondents = $firstVar ? count($formattedData[$firstVar]) : 0;

            // Dapatkan halaman saat ini dari query string
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            // Potong data untuk halaman saat ini
            $currentPageData = [];
            $start = ($currentPage - 1) * $perPage;

            foreach ($formattedData as $var => $values) {
                $currentPageData[$var] = array_slice($values, $start, $perPage);
            }

            // Buat paginator manual
            $values = new LengthAwarePaginator(
                $currentPageData,
                $totalRespondents,
                $perPage,
                $currentPage,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'query' => $request->query()
                ]
            );

            $recordIds = [$reference]; // Karena kita hanya menampilkan satu record
            session(['recordIds' => $recordIds]);

            return view('dashboard', [
                'recordIds' => $recordIds,
                'formattedData' => $currentPageData, // Gunakan data yang sudah dipotong
                'success' => session('success'),
                'reference' => $reference,
                'referenceData' => $referenceData,
                'sessionRecordId' => $recordIds,
                'pagination' => $values->withQueryString()->links(),
                'values' => $values,
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
