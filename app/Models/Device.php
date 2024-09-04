<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';

    protected $fillable = [
        'manager_id',
        'user_name',
        'password',
        'logo',
        'header_one',
        'header_two',
        'footer'
    ];

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'manager_device_routes')
                    ->withPivot('manager_id')
                    ->withTimestamps();
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
