<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteFare extends Model
{
    use HasFactory;

    protected $table = 'route_fares';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
