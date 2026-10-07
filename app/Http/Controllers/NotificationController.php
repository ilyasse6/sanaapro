<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // N'oubliez pas d'importer le modèle User

class NotificationController extends Controller
{
    /**
     * Display all notifications (read and unread) for the authenticated user.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->back()->with('error', 'Utilisateur introuvable');
        }
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(20);
        return view('user.notifications.index', compact('notifications'));
    }
    /**
     * Récupère les notifications non lues de l'utilisateur au format JSON.
     */
    public function getUnread()
    {
        if (!Auth::check()) {
            return response()->json(['data' => []], 401);
        }
        $user = User::find(Auth::id());
        return $user ? response()->json($user->unreadNotifications) : response()->json(['data' => []]);
    }

    /**
     * Marque une notification spécifique comme lue.
     */
    public function markAsRead($notificationId)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Non authentifié'], 401);
        }
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Utilisateur introuvable'], 404);
        }
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error', 'message' => 'Notification introuvable'], 404);
    }
}