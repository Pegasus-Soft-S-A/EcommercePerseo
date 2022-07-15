<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudades extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'ciudades';
    protected $primaryKey = 'ciudadesid';
    protected $rememberTokenName = false;
    public $timestamps = false;
}
