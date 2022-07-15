<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $primaryKey = 'pedidosid';
    public $timestamps = false;

    protected $fillable = ['clientesid', 'emision', 'pedidos_codigo', 'facturadoresid', 'centro_costosid', 'vendedoresid', 'concepto', 'origen', 'subtotalsiniva', 'subtotalconiva', 'total_descuento', 'subtotalneto', 'total_iva', 'total', 'estado', 'fechacreacion', 'usuariocreacion'];
}
