<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    protected $connection = 'sistema';
    protected $table = 'sis_log_sistema';
    protected $primaryKey = 'sis_log_sistemasid';
    public $timestamps = false;
}
