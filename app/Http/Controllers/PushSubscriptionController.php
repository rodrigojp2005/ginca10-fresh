<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.auth' => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);
        $user->updatePushSubscription($request->endpoint, $request->keys['p256dh'], $request->keys['auth']);
        Log::info('Push subscription registrada', [
            'user_id' => $user->id,
            'endpoint_hash' => substr(hash('sha256', $request->endpoint),0,16)
        ]);
        return response()->json(['success' => true]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);
        $user->deletePushSubscription($request->endpoint);
        Log::info('Push subscription removida', [
            'user_id' => $user->id,
            'endpoint_hash' => substr(hash('sha256', $request->endpoint),0,16)
        ]);
        return response()->json(['success' => true]);
    }
}
