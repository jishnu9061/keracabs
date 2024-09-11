<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $table = 'trips';

    protected $fillable = [
        'trip_name',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'full_ticket',
        'half_ticket',
        'student_ticket',
        'language_ticket',
        'physical_ticket'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
