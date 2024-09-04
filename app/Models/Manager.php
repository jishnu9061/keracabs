<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    use HasFactory;

    protected $table = 'managers';

    protected $fillable = [
        'name',
        'user_name',
        'password',
        'contact'
    ];

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'manager_device_routes')
                    ->withPivot('device_id')
                    ->withTimestamps();
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
