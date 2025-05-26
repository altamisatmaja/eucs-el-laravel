<?php

namespace App\Imports;

use App\Models\RecordValue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RecordsImport implements ToModel, WithHeadingRow
{
    protected $recordId;

    public function __construct($recordId)
    {
        $this->recordId = $recordId;
    }

    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['variable']) || empty($row['value'])) {
            return null;
        }

        return new RecordValue([
            'record_id' => $this->recordId,
            'variable' => $row['variable'],
            'value' => $row['value']
        ]);
    }
}