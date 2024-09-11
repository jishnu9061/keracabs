<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Manager extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'managers';

    protected $fillable = [
        'name',
        'user_name',
        'password',
        'contact'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
