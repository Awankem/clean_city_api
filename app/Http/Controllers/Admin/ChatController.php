<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Report;
use App\Services\ReportNotificationService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function show($reportId)
    {
        $report = Report::with(['user', 'category'])->findOrFail($reportId);
        
        $messages = ChatMessage::with('sender')
            ->where('report_id', $reportId)
            ->orderBy('created_at', 'asc')
            ->get();

        // If polling via AJAX, return partial view or JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($messages);
        }

        return view('admin.chat.show', compact('report', 'messages'));
    }

    public function store(Request $request, $reportId, ReportNotificationService $notifications)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $report = Report::findOrFail($reportId);

        $message = ChatMessage::create([
            'report_id' => $report->id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // Notify citizen
        $notifications->notifyChatMessage($message, isFromAdmin: true);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($message->load('sender'));
        }

        return redirect()->route('admin.chat.show', $report->id);
    }

    public function markRead($reportId)
    {
        ChatMessage::where('report_id', $reportId)
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }
}
