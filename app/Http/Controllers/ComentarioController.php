<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Gincana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function index($gincana_id)
    {
        $comentarios = Comentario::where('gincana_id', $gincana_id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($comentarios);
    }

    public function store(Request $request)
    {
        $request->validate([
            'gincana_id' => 'required|exists:gincanas,id',
            'conteudo' => 'required|string|max:500'
        ]);

        $comentario = Comentario::create([
            'gincana_id' => $request->gincana_id,
            'user_id' => Auth::id(),
            'conteudo' => $request->conteudo
        ]);

        $comentario->load('user');

        return response()->json([
            'success' => true,
            'comentario' => $comentario
        ]);
    }
}
