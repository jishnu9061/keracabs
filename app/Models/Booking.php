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
        'message'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
