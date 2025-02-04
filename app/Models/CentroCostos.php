<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentroCostos extends Model
{
    protected $connection = "empresa";
    protected $table = 'centros_costos';
    protected $primaryKey = 'centros_costosid';
    protected $rememberTokenName = false;
    public $timestamps = false;
}
