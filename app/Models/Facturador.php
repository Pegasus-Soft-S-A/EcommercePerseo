<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facturador extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'facturadores';
}
