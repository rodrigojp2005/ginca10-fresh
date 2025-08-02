<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = [
        'gincana_id',
        'user_id',
        'conteudo'
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
