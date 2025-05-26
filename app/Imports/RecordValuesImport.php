<?php

namespace App\Imports;

use App\Models\RecordValue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RecordValuesImport implements ToCollection, WithHeadingRow
{
    protected $recordId;

    public function __construct($recordId)
    {
        $this->recordId = $recordId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            foreach ($row as $variable => $value) {
                if ($value === null) continue;
                
                RecordValue::create([
                    'record_id' => $this->recordId,
                    'variable' => $variable,
                    'value' => $value
                ]);
            }
        }
    }
}