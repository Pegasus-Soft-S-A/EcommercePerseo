<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentarios extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    public $timestamps = false;
    protected $table = 'ecommerce_comentarios';
    protected $primaryKey = 'ecommerce_comentariosid';
}
