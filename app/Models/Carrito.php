<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'ecommerce_carrito';
    protected $primaryKey = 'ecommerce_carritosid';
    protected $fillable = ['clientesid', 'precio', 'medidasid', 'productosid', 'cantidad', 'cantidadfactor', 'iva', 'precio', 'precioiva', 'descuento', 'usuario_temporalid','almacenesid'];
    public $timestamps = false;
}
