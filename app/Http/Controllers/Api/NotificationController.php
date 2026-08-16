<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $notifications = $request->user()->notifications()->latest('date_notification')->paginate(15);

        return NotificationResource::collection($notifications)->additional([
            'unread_count' => $request->user()->notifications()->where('est_lue', false)->count(),
        ]);
    }

    public function show(Request $request, Notification $notification): JsonResponse
    {
        $this->ensureOwnership($request, $notification);
        $notification->update(['est_lue' => true]);

        return response()->json(['data' => new NotificationResource($notification)]);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $this->ensureOwnership($request, $notification);
        $notification->update(['est_lue' => true]);

        return response()->json(['data' => new NotificationResource($notification)]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->notifications()->where('est_lue', false)->update(['est_lue' => true]);

        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
    }

    public function destroy(Request $request, Notification $notification): JsonResponse
    {
        $this->ensureOwnership($request, $notification);
        $notification->delete();

        return response()->json(['message' => 'Notification supprimée.']);
    }

    private function ensureOwnership(Request $request, Notification $notification): void
    {
        abort_unless($notification->id_utilisateur === $request->user()->id_utilisateur, 403);
    }
}
