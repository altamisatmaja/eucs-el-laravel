<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordValue extends Model
{
    use HasFactory;

    protected $fillable = ['record_id', 'variable', 'value'];

    public function record()
    {
        return $this->belongsTo(Record::class);
    }

}
