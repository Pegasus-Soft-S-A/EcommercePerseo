<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clientes extends Authenticatable
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'clientes';
    protected $primaryKey = 'clientesid';
    protected $rememberTokenName = false;
    public $timestamps = false;
}
