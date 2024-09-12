<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceRouteAssignment extends Model
{
    use HasFactory;

    protected $table = 'device_route_assignments';

    protected $fillable = [
        'device_id',
        'route_id',
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
