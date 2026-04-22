<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentNotification;

class PaymentNotificationController extends Controller
{
    public function adminIndex()
    {
        $notifications = PaymentNotification::with('order')
            ->where('type', 'cashier')
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function getCashierNotifications()
    {
        $notifications = PaymentNotification::with('order')
            ->where('type', 'cashier')
            ->where('is_read', false)
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    public function getCustomerNotifications(Request $request)
    {
        $orderNumber = $request->order_number;
        
        if (!$orderNumber) {
            return response()->json([]);
        }

        $notifications = PaymentNotification::whereHas('order', function($query) use ($orderNumber) {
            $query->where('order_number', $orderNumber);
        })
        ->where('type', 'customer')
        ->where('is_read', false)
        ->latest()
        ->get();

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = PaymentNotification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllRead()
    {
        PaymentNotification::where('type', 'cashier')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}