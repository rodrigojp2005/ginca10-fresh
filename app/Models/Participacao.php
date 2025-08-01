<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participacao extends Model
{
    use HasFactory;

    protected $table = 'participacoes';

    protected $fillable = [
        'user_id',
        'gincana_id',
        'pontuacao',
        'status',
        'inicio_participacao',
        'fim_participacao',
        'tempo_total_segundos',
        'locais_visitados'
    ];

    protected $casts = [
        'inicio_participacao' => 'datetime',
        'fim_participacao' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gincana()
    {
        return $this->belongsTo(Gincana::class);
    }
}
