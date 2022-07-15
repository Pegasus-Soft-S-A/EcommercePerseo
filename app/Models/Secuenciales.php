<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secuenciales extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    public $timestamps = false;
    protected $primaryKey = 'secuencialesid';
}
