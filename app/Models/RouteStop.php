<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    use HasFactory;

    protected $table = 'route_stops';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
