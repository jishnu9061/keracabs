<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'vehicle',
        'message',
        'vehicle_type',
        'start_date',
        'end_date',
        'start_time',
        'end_time'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
