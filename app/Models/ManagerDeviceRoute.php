<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerDeviceRoute extends Model
{
    use HasFactory;

    protected $table = 'manager_device_routes';

    protected $fillable = [
        'manager_id',
        'device_id',
        'route_id',
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
