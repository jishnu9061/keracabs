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

    public function managers()
    {
        return $this->belongsToMany(Manager::class, 'manager_device_routes')
                    ->withPivot('device_id')
                    ->withTimestamps();
    }

    public function devices()
    {
        return $this->belongsToMany(Device::class, 'manager_device_routes')
                    ->withPivot('manager_id')
                    ->withTimestamps();
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
