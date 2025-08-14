<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GincanaCommentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gincana_id',
        'unread_count',
        'last_comentario_id',
        'last_preview',
        'last_author_name',
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
