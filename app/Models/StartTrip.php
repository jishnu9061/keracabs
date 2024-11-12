<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartTrip extends Model
{
    use HasFactory;

    protected $table = 'start_trips';

    protected $fillable = [
        'route_id',
        'start_id',
        'stop_id',
        'trip_name',
        'status',
        'device_id',
    ];

}
