<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = "ecommerce_lista_deseos";
    protected $primaryKey = 'ecommerce_lista_deseosid';
    public $timestamps = false;
}
