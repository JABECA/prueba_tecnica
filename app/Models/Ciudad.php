<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ciudad extends Model
{
    use SoftDeletes;
    protected $table = 'ciudades';
    protected $primaryKey = 'idciudad';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;


    protected $fillable = ['nombre', 'activo'];

    public function viajesOrigen() {
        return $this->hasMany(Viaje::class, 'idciudad_origen', 'idciudad');
    }

    public function viajesDestino() {
        return $this->hasMany(Viaje::class, 'idciudad_destino', 'idciudad');
    }
}
