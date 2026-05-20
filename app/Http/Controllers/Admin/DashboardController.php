<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\StatusHistory;
use App\Services\ReportNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        // Geo-located reports for the dashboard map widget
        $mapReports = Report::whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'latitude', 'longitude', 'priority_score', 'category_id')
            ->with('category')
            ->orderBy('priority_score', 'desc')
            ->take(50)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports', 'mapReports'));
    }

    public function reports(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $reports = Report::with('category', 'user')
            ->statusFilter($status)
            ->search($search)
            ->orderBy('priority_score', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact('reports', 'status', 'search'));
    }

    public function showReport($id)
    {
        $report = Report::with(['category', 'user', 'images', 'statusHistory.changedBy'])->findOrFail($id);

        $currentStatus = ReportStatus::tryFromString($report->status);
        $allowedNextStatuses = $currentStatus
            ? $currentStatus->allowedNext()
            : [];

        return view('admin.reports.show', compact('report', 'allowedNextStatuses', 'currentStatus'));
    }

    public function updateStatus(Request $request, $id, ReportNotificationService $notifications)
    {
        $report = Report::findOrFail($id);
        $oldStatus = $report->status;

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'note' => 'nullable|string|max:1000',
        ]);

        $current = ReportStatus::tryFromString($oldStatus);
        $next = ReportStatus::tryFromString($request->status);

        if (!$current || !$next) {
            throw ValidationException::withMessages([
                'status' => 'Invalid report status.',
            ]);
        }

        if ($current === $next) {
            return redirect()->back()->with('success', 'Status is already ' . $current->label() . '.');
        }

        if (!$current->canTransitionTo($next)) {
            throw ValidationException::withMessages([
                'status' => "Cannot change status from {$current->label()} back to {$next->label()}. "
                    . 'Workflow only moves forward: Pending review → In review → Resolved.',
            ]);
        }

        $report->status = $next->value;
        $report->save();

        StatusHistory::create([
            'report_id' => $report->id,
            'changed_by' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $next->value,
            'note' => $request->note,
        ]);

        $notifications->notifyStatusChanged($report, $oldStatus, $next->value, auth()->id());

        return redirect()->back()->with('success', 'Status updated to ' . $next->label() . '. Citizen notified.');
    }

    public function exportReports(Request $request): StreamedResponse
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $filename = 'reports-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($status, $search) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Report ID', 'Submitted', 'Citizen', 'Category', 'Status', 'Priority', 'Location']);

            Report::with(['category', 'user'])
                ->statusFilter($status)
                ->search($search)
                ->orderByDesc('created_at')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $report) {
                        fputcsv($handle, [
                            '#CC-' . str_pad((string) $report->id, 4, '0', STR_PAD_LEFT),
                            $report->created_at->format('Y-m-d H:i'),
                            $report->user->name ?? 'Anonymous',
                            $report->category->name ?? 'Uncategorized',
                            $report->status,
                            $report->priority_score,
                            $report->location_name ?? "{$report->latitude}, {$report->longitude}",
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Display the analytics dashboard.
     */
    public function analytics()
    {
        $isPgSql = \DB::getDriverName() === 'pgsql';
        // Data aggregation for charts
        $analytics = [
            'reportsByCategory' => Report::join('categories', 'reports.category_id', '=', 'categories.id')
                ->select(\DB::raw('categories.name as label, count(*) as value'))
                ->groupBy('categories.name')
                ->get(),
            'monthlyTrends' => Report::select($isPgSql 
                ? \DB::raw('TO_CHAR(created_at, \'Mon\') as month, count(*) as count')
                : \DB::raw('DATE_FORMAT(created_at, "%b") as month, count(*) as count'))
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy(\DB::raw('MIN(created_at)'))
                ->get(),
            'resolutionRate' => Report::count() > 0 
                ? (Report::where('status', 'resolved')->count() / Report::count()) * 100 
                : 0,
            'avgPriority' => Report::avg('priority_score') ?? 0,
        ];

        return view('admin.analytics', compact('analytics'));
    }

    public function exportAnalytics(): StreamedResponse
    {
        $isPgSql = DB::getDriverName() === 'pgsql';
        $filename = 'analytics-' . now()->format('Y-m-d-His') . '.csv';

        $byCategory = Report::join('categories', 'reports.category_id', '=', 'categories.id')
            ->select(DB::raw('categories.name as label, count(*) as value'))
            ->groupBy('categories.name')
            ->get();

        $monthly = Report::select($isPgSql
            ? DB::raw('TO_CHAR(created_at, \'Mon YYYY\') as month, count(*) as count')
            : DB::raw('DATE_FORMAT(created_at, "%b %Y") as month, count(*) as count'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(created_at)'))
            ->get();

        $total = Report::count();
        $resolved = Report::where('status', 'resolved')->count();

        return response()->streamDownload(function () use ($byCategory, $monthly, $total, $resolved) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Summary']);
            fputcsv($handle, ['Total reports', $total]);
            fputcsv($handle, ['Resolved', $resolved]);
            fputcsv($handle, ['Resolution rate %', $total > 0 ? round(($resolved / $total) * 100, 1) : 0]);
            fputcsv($handle, ['Avg priority', round((float) Report::avg('priority_score'), 1)]);
            fputcsv($handle, []);

            fputcsv($handle, ['Reports by category']);
            fputcsv($handle, ['Category', 'Count']);
            foreach ($byCategory as $row) {
                fputcsv($handle, [$row->label, $row->value]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['Monthly volume (last 6 months)']);
            fputcsv($handle, ['Month', 'Count']);
            foreach ($monthly as $row) {
                fputcsv($handle, [$row->month, $row->count]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Display the hotspot analysis map.
     */
    public function hotspots()
    {
        $hotspots = Report::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'latitude', 'longitude', 'priority_score', 'category_id', 'status', 'location_name', 'created_at')
            ->with(['category', 'images' => function ($q) {
                $q->select('id', 'report_id', 'image_path')->limit(1);
            }])
            ->orderBy('priority_score', 'desc')
            ->get();

        $categories = \App\Models\Category::withCount('reports')->get();

        return view('admin.hotspots', compact('hotspots', 'categories'));
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

    public function exportAudit(Request $request): StreamedResponse
    {
        $filename = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Administrator', 'Report ID', 'Old status', 'New status', 'Note']);

            StatusHistory::with(['report', 'changedBy'])
                ->orderByDesc('created_at')
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $log) {
                        fputcsv($handle, [
                            $log->created_at->format('Y-m-d H:i'),
                            $log->changedBy->name ?? 'System',
                            $log->report_id,
                            $log->old_status,
                            $log->new_status,
                            $log->note ?? '',
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
