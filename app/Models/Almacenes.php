<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacenes extends Model
{
    protected $connection = "empresa";
    protected $table = 'almacenes';
    protected $primaryKey = 'almacenesid';
    protected $rememberTokenName = false;
    public $timestamps = false;

}
