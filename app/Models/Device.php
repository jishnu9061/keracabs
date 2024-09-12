<?php
namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Authenticatable
{
    use HasApiTokens, Notifiable;

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

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
