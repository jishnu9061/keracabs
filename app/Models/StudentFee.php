<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    use HasFactory;

    protected $table = 'student_fees';

    protected $fillable = [
        'price_date',
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
