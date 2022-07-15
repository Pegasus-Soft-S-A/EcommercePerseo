<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientosInventariosAlmacenes extends Model
{
    protected $connection = "empresa";
    protected $table = 'movinventarios_almacenes';
    protected $primaryKey = 'movinventarios_almacenesid';
    protected $rememberTokenName = false;
    public $timestamps = false;
}
