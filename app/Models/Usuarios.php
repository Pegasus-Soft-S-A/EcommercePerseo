<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuarios extends Authenticatable
{
    use HasFactory;

    protected $connection = 'sistema';
    protected $table = 'sis_usuarios';
    protected $primaryKey = 'sis_usuariosid';

    protected $rememberTokenName = false;
    public $timestamps = false;

    public function getAuthPassword()
    {
        return $this->contrasena;
    }
}
