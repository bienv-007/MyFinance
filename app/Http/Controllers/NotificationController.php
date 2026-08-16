<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()
            ->latest('date_notification')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function show(Request $request, Notification $notification): View
    {
        $this->ensureOwnership($request, $notification);
        $notification->update(['est_lue' => true]);

        return view('notifications.show', compact('notification'));
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->where('est_lue', false)->update(['est_lue' => true]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy(Request $request, Notification $notification): RedirectResponse
    {
        $this->ensureOwnership($request, $notification);
        $notification->delete();

        return redirect()->route('notifications.index')->with('success', 'Notification supprimée.');
    }

    public function destroyAll(Request $request): RedirectResponse
    {
        $request->user()->notifications()->delete();

        return back()->with('success', 'Toutes les notifications ont été supprimées.');
    }

    private function ensureOwnership(Request $request, Notification $notification): void
    {
        abort_unless($notification->id_utilisateur === $request->user()->id_utilisateur, 404);
    }
}
