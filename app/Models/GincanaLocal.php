<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GincanaLocal extends Model
{
    use HasFactory;
    
    protected $table = 'gincana_locais';
    
    protected $fillable = [
        'gincana_id', 
        'latitude', 
        'longitude'
    ];

    public function gincana()
    {
        return $this->belongsTo(Gincana::class);
    }
}
