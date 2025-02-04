<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parroquias extends Model
{
    protected $connection = "empresa";
    protected $table = 'parroquias';
    protected $primaryKey = 'parroquiasid';
    protected $rememberTokenName = false;
    public $timestamps = false;
}
