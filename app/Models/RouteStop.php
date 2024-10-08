<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    use HasFactory;

    protected $table = 'route_stops';

    protected $fillable = [
        'stop_name',
        'stop_sequence',
        'route_id',
        'price'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
