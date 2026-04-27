<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Daftar semua notifikasi',
            'data' => Notification::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer',
            'type' => 'required|string',
            'message' => 'required|string',
        ]);

        $notification = Notification::create([
            'employee_id' => $validated['employee_id'],
            'type' => $validated['type'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Notifikasi berhasil dibuat',
            'data' => $notification
        ], 201);
    }

    public function showByEmployee($employee_id)
    {
        $notifications = Notification::where('employee_id', $employee_id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Daftar notifikasi karyawan',
            'employee_id' => $employee_id,
            'data' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json([
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }

        $notification->update([
            'is_read' => true
        ]);

        return response()->json([
            'message' => 'Notifikasi berhasil ditandai sudah dibaca',
            'data' => $notification
        ]);
    }
}
