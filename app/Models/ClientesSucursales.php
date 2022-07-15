<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientesSucursales extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'clientes_sucursales';
    protected $primaryKey = 'clientes_sucursalesid';
    public $timestamps = false;

    protected $fillable = ['clientesid', 'ciudadesid', 'direccion', 'telefono1', 'fechamodificacion', 'usuariomodificacion', 'fechacreacion', 'usuariocreacion'];
}
