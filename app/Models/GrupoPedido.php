<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoPedido extends Model
{
    use HasFactory;
    protected $table = 'grupo_pedido';
    public $timestamps = false;
}
