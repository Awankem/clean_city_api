<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\StatusHistory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'in_progress' => Report::where('status', 'in_progress')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
        ];

        $recentReports = Report::with('category', 'user')
            ->orderBy('priority_score', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports'));
    }

    public function reports()
    {
        $reports = Report::with('category', 'user')
            ->orderBy('priority_score', 'desc')
            ->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    public function showReport($id)
    {
        $report = Report::with(['category', 'user', 'images', 'statusHistory.changedBy'])->findOrFail($id);
        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $oldStatus = $report->status;

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'note' => 'nullable|string'
        ]);

        $report->status = $request->status;
        $report->save();

        // Log the status change
        StatusHistory::create([
            'report_id' => $report->id,
            'changed_by' => 1, // Placeholder for logged in admin (we'll use auth()->id() later)
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'note' => $request->note
        ]);

        return redirect()->back()->with('success', 'Report status updated successfully.');
    }
}
