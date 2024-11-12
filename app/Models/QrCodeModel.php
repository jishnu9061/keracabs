<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCodeModel extends Model
{
    use HasFactory;

    protected $table = 'qr_codes';

    protected $fillable = [
       'image'
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
