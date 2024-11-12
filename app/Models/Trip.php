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
        'physical_ticket',
        'stage_id',
        'start_id',
        'stop_id',
        'device_id',
        'total_amount',
        'total_expense',
        'net_total',
        'route_status',
        'start_day_id',
        'trip_id'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
