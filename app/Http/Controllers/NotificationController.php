<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $unread = $user->unreadNotifications()->latest()->limit(20)->get();
        return response()->json([
            'unread_count' => $unread->count(),
            'notifications' => $unread->map(function($n){
                return [
                    'id' => $n->id,
                    'type' => $n->data['type'] ?? null,
                    'gincana_id' => $n->data['gincana_id'] ?? null,
                    'gincana_nome' => $n->data['gincana_nome'] ?? null,
                    'autor' => $n->data['autor'] ?? null,
                    'conteudo' => $n->data['conteudo'] ?? null,
                    'created_at' => $n->created_at->toDateTimeString(),
                ];
            })
        ]);
    }

    public function markRead(Request $request)
    {
        $request->validate(['id' => 'nullable|string']);
        $user = Auth::user();
        if($request->id){
            $user->unreadNotifications()->where('id',$request->id)->update(['read_at' => now()]);
        } else {
            $user->unreadNotifications()->update(['read_at' => now()]);
        }
        return response()->json(['success' => true]);
    }
}
