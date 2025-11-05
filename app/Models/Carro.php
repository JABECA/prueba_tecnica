<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carro extends Model
{
    use SoftDeletes;
    protected $table = 'carros';
    protected $primaryKey = 'idcarro';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;


    protected $fillable = ['placa', 'color', 'fecha_ingreso'];

    public function viajes() {
        return $this->hasMany(Viaje::class, 'idcarro', 'idcarro');
    }
}
