<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametrosEmpresa extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'parametros_empresa';
    protected $primaryKey = 'parametros_empresaid';
    public $timestamps = false;
}
