<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    use HasFactory;

    protected $connection = 'sistema';
    protected $table = 'sis_empresas';
    protected $primaryKey = 'sis_empresasid';

    protected $rememberTokenName = false;
    public $timestamps = false;
}
