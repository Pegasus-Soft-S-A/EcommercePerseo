<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facturas extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'facturas';
    protected $primaryKey = 'facturasid';
    public $timestamps = false;
}
