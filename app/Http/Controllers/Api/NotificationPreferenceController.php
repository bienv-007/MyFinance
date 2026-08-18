<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $preferences = UserNotificationPreference::firstOrCreate([
            'id_utilisateur' => $request->user()->id_utilisateur,
        ], [
            'notif_son' => true,
            'notif_vibration' => true,
            'notif_navigateur' => false,
        ]);

        return response()->json(['data' => $preferences]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'notif_son' => 'sometimes|boolean',
            'notif_vibration' => 'sometimes|boolean',
            'notif_navigateur' => 'sometimes|boolean',
        ]);

        $preferences = UserNotificationPreference::firstOrCreate([
            'id_utilisateur' => $request->user()->id_utilisateur,
        ], [
            'notif_son' => true,
            'notif_vibration' => true,
            'notif_navigateur' => false,
        ]);

        $preferences->update($validated);

        return response()->json(['data' => $preferences]);
    }
}
