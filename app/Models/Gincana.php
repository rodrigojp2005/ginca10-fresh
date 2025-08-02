<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gincana extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nome',
        'duracao',
        'latitude',
        'longitude',
        'contexto',
        'privacidade',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class)->orderBy('created_at', 'desc');
    }

    // Relacionamento temporariamente desabilitado
    // public function locais()
    // {
    //     return $this->hasMany(GincanaLocal::class);
    // }

    public function participacoes()
    {
        return $this->hasMany(Participacao::class);
    }

    public function participantes()
    {
        return $this->belongsToMany(User::class, 'participacoes')
                    ->withPivot('pontuacao', 'status', 'tempo_total_segundos', 'locais_visitados')
                    ->withTimestamps();
    }
}
