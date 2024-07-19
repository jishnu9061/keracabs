<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $table= 'registration';

    protected $fillable = [
        'name',
        'number',
        'vehicle_type',
        'seating_capacity',
        'vehicle_number',
        'parking_location',
        'district',
        'vehicle_photo',
        'driver_image',
        'whatsapp_number'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
