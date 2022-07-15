<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturasDetalles extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'facturas_detalles';
    protected $primaryKey = 'facturas_detallesid';
    public $timestamps = false;
}
