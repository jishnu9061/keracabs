<?php
namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $table = 'devices';

    protected $fillable = [
        'manager_id',
        'user_name',
        'password',
        'logo',
        'header_one',
        'header_two',
        'footer',
        'qr_code',
        'gpay_id'
    ];

    protected $casts = [
        'manager_id' => 'integer',
        'user_name' => 'string',
        'password' => 'string',
        'logo' => 'string',
        'header_one' => 'string',
        'header_two' => 'string',
        'footer' => 'string',
        'qr_code' => 'integer',
        'gpay_id' => 'integer',
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
