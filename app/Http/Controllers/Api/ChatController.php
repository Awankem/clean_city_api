<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Report;
use App\Services\ReportNotificationService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request, $reportId)
    {
        $report = Report::findOrFail($reportId);

        // Ensure user owns the report
        if ($report->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = ChatMessage::with('sender:id,name,role')
            ->where('report_id', $reportId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, $reportId, ReportNotificationService $notifications)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $report = Report::findOrFail($reportId);

        if ($report->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = ChatMessage::create([
            'report_id' => $report->id,
            'sender_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        // Notify admins
        $notifications->notifyChatMessage($message, isFromAdmin: false);

        return response()->json($message->load('sender:id,name,role'), 201);
    }

    public function markRead(Request $request, $reportId)
    {
        $report = Report::findOrFail($reportId);

        if ($report->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        ChatMessage::where('report_id', $reportId)
            ->where('sender_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function unreadCount(Request $request)
    {
        $count = ChatMessage::whereHas('report', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->where('sender_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}
