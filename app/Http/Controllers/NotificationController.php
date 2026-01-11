<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

   
      public function show($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();

        // Optionally mark it as read immediately when viewing
        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

        // / NotificationController.php
    public function count()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    public function markRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        if ($notification) {
            $notification->markAsRead();
            return back()->with('success','Marked read.');
        }
        return back()->with('error','Notification not found.');
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success','All marked read.');
    }
}
