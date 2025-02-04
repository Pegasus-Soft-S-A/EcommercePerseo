<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincias extends Model
{
    protected $connection = "empresa";
    protected $table = 'provincias';
    protected $primaryKey = 'provinciasid';
    protected $rememberTokenName = false;
    public $timestamps = false;
}
