<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    protected $fillable = [
        'nombre',
        'puesto',
        'sueldo_diario',
        'dias_trabajados',
        'sueldo_semanal',
        'deposito_bancario',
        'sueldo_a_pagar',
        'sueldo_fijo',
        'valor_bono',
        'bono_obtenido',
    ];
}
