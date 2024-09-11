<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $table = 'routes';

    protected $fillable = [
        'device_id',
        'route_from',
        'route_to',
        'type',
        'minimum_charge'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
