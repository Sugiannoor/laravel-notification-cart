<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\NotificationUser;

class NotificationController extends Controller
{
    public function detail(Request $request)
    {
        $userId = Auth::user()->id;
        

        
        if (!NotificationUser::where('notification_id', $request->notification_id)
                            ->where('user_id', $userId)
                            ->exists()) {
            NotificationUser::create([
                "notification_id" => $request->notification_id,
                "user_id" => $userId,
                "is_read" => true
            ]);
        }

        $notification = Notification::select("id", "message as description", "created_at")
                            ->find($request->notification_id);

        return response()->json([
            "code" => 200,
            "status" => "OK",
            "data" => [
                "id" => $notification->id,
                "description" => $notification->description,
                "created_at" => $notification->created_at->format("Y-m-d H:i:s"),
                "elapsed_time" => $notification->created_at->diffForHumans()
            ]
        ], 200);
    }

    public function getAll()
    {
        $userId = Auth::user()->id;
        
        $data = Notification::select("notifications.id", "notifications.message as description", "notifications.created_at")
                    ->join('notification_users', 'notifications.id', '=', 'notification_users.notification_id')
                    ->where('notification_users.user_id', $userId)
                    ->orderBy("notifications.created_at", "asc")
                    ->get()
                    ->map(function($notification){
                        return [
                            "id" => $notification->id,
                            "description" => $notification->description,
                            "time" => $notification->created_at->format("H:i"),
                            "elapsed_time" => $notification->created_at->diffForHumans()
                        ];
                    });

        return response()->json([
            "code" => 200,
            "status" => "OK",
            "data" => $data
        ], 200);
    }

    public function getLatest()
    {
        $userId = Auth::user()->id;

        $data = Notification::select("notifications.id", "notifications.message as description", 'notifications.created_at', 'notifications.type', 'notifications.data_id')
                ->join('notification_users', 'notifications.id', '=', 'notification_users.notification_id')
                ->where('notification_users.user_id', $userId)
                ->latest("notifications.created_at")
                ->get()
                ->map(function($notification){
                    return [
                        "description" => $notification->description,
                        "time" => $notification->created_at->format("H:i"),
                        "elapsed_time" => $notification->created_at->diffForHumans(),
                        "type" => $notification->type,
                        "data_id" => $notification->data_id,
                    ];
                });

        return response()->json([
            "code" => 200,
            "status" => "OK",
            "data" => $data
        ], 200);
    }
}
