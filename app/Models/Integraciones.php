<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integraciones extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'integraciones';
    protected $primaryKey = 'integracionesid';
    public $timestamps = false;
}
