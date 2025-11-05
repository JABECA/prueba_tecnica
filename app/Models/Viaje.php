<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Viaje extends Model
{   
    use SoftDeletes;
    protected $table = 'viajes';
    protected $primaryKey = 'idviaje';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['idcarro','idciudad_origen','idciudad_destino','tiempo_horas','fecha'];

    public function carro() {
        return $this->belongsTo(Carro::class, 'idcarro', 'idcarro');
    }

    public function origen() {
        return $this->belongsTo(Ciudad::class, 'idciudad_origen', 'idciudad');
    }

    public function destino() {
        return $this->belongsTo(Ciudad::class, 'idciudad_destino', 'idciudad');
    }
}
