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
            'changed_by' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'note' => $request->note
        ]);

        return redirect()->back()->with('success', 'Report status updated successfully.');
    }

    /**
     * Display the analytics dashboard.
     */
    public function analytics()
    {
        // Data aggregation for charts
        $analytics = [
            'reportsByCategory' => Report::join('categories', 'reports.category_id', '=', 'categories.id')
                ->select(\DB::raw('categories.name as label, count(*) as value'))
                ->groupBy('categories.name')
                ->get(),
            'monthlyTrends' => Report::select(\DB::raw('DATE_FORMAT(created_at, "%b") as month, count(*) as count'))
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('created_at')
                ->get(),
            'resolutionRate' => Report::count() > 0 
                ? (Report::where('status', 'resolved')->count() / Report::count()) * 100 
                : 0,
            'avgPriority' => Report::avg('priority_score') ?? 0,
        ];

        return view('admin.analytics', compact('analytics'));
    }

    /**
     * Display the hotspot analysis map.
     */
    public function hotspots()
    {
        $hotspots = Report::whereIn('status', ['pending', 'in_progress'])
            ->select('id', 'latitude', 'longitude', 'priority_score', 'category_id')
            ->with('category')
            ->get();

        return view('admin.hotspots', compact('hotspots'));
    }

    /**
     * Display the administrative audit trail.
     */
    public function audit()
    {
        $logs = StatusHistory::with(['report', 'changedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.audit', compact('logs'));
    }
}
