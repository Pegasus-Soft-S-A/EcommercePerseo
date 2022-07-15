<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subgrupos extends Model
{
    use HasFactory;
    protected $connection = "empresa";
    protected $table = 'productos_subgrupo';
}
