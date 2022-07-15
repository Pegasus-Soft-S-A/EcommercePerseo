<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidosDetalles extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'pedidos_detalles';
    protected $primaryKey = 'pedidos_detallesid';
    public $timestamps = false;

    protected $fillable = ['pedidosid', 'centro_costosid', 'productosid', 'medidasid', 'almacenesid', 'cantidaddigitada', 'cantidad', 'cantidadfactor', 'precio', 'iva', 'precioiva', 'preciovisible', 'descuento'];
}
