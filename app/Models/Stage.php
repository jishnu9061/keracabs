<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    protected $table = 'stages';

    protected $fillable = [
        'route_id',
        'stage_data'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
