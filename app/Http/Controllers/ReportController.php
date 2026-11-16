<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditException;
use App\Models\Department;
use App\Models\License;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function downloadPdf(string $type)
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('reports.view'), 403);

        $date = now()->format('d M Y');

        switch ($type) {
            case 'complete-register':
                $title = 'Complete Asset Register';
                $data = $actor->scopeDepartmentQuery(Asset::orderBy('id'), 'department')->get();
                $view = 'reports.pdf.complete-register';
                break;

            case 'by-department':
                $title = 'Assets by Department Report';
                $departments = $actor->scopeDepartmentQuery(Department::query(), 'name')->get();
                $assetsByDept = $actor->scopeDepartmentQuery(Asset::whereNotNull('department'), 'department')
                    ->get()
                    ->groupBy('department');
                $data = compact('departments', 'assetsByDept');
                $view = 'reports.pdf.by-department';
                break;

            case 'assignment-history':
                $title = 'Asset Assignment & Transfer History';
                $txQuery = Transaction::orderByDesc('created_at')->limit(100);
                if ($actor->isDepartmentScoped()) {
                    $scopedIds = $actor->scopeDepartmentQuery(Asset::query(), 'department')->pluck('id');
                    $txQuery->whereIn('asset_id', $scopedIds);
                }
                $data = $txQuery->get();
                $view = 'reports.pdf.assignment-history';
                break;

            case 'warranty-expiry':
                $title = 'Upcoming Warranty Expirations';
                $data = $actor->scopeDepartmentQuery(Asset::whereNotNull('warranty_end'), 'department')
                    ->orderBy('warranty_end')
                    ->limit(50)
                    ->get();
                $view = 'reports.pdf.warranty-expiry';
                break;

            case 'audit-exceptions':
                $title = 'Audit Exceptions & Discrepancies';
                $data = $actor->scopeDepartmentQuery(AuditException::orderByDesc('created_at'), 'owner_department')->get();
                $view = 'reports.pdf.audit-exceptions';
                break;

            case 'license-utilization':
                abort_if($actor->isDepartmentScoped(), 403);
                $title = 'Software License Utilization Report';
                $data = License::all();
                $view = 'reports.pdf.license-utilization';
                break;

            default:
                abort(404, 'Unknown report type');
        }

        $pdf = Pdf::loadView($view, [
            'title' => $title,
            'date' => $date,
            'data' => $data,
            'showFinancial' => $actor->canViewFinancialData(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download(str_replace(' ', '_', strtolower($title)).'_'.date('Ymd').'.pdf');
    }

    public function exportCsv(string $type)
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('reports.view'), 403);

        $showFinancial = $actor->canViewFinancialData();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$type}_".date('Ymd').'.csv"',
        ];

        $callback = function () use ($type, $actor, $showFinancial) {
            $handle = fopen('php://output', 'w');

            if ($type === 'complete-register') {
                $columns = ['Asset ID', 'Name', 'Category', 'Serial', 'Assigned To', 'Location', 'Status'];
                if ($showFinancial) {
                    $columns[] = 'Cost (MYR)';
                }
                $columns[] = 'Warranty End';
                fputcsv($handle, $columns);

                $assets = $actor->scopeDepartmentQuery(Asset::orderBy('id'), 'department')->get();
                foreach ($assets as $a) {
                    $row = [$a->id, $a->name, $a->category_name, $a->serial_number, $a->assigned_user, $a->assigned_location, $a->status];
                    if ($showFinancial) {
                        $row[] = $a->purchase_cost;
                    }
                    $row[] = $a->warranty_end;
                    fputcsv($handle, $row);
                }
            } else {
                fputcsv($handle, ['ID', 'Type', 'Record Date']);
                fputcsv($handle, [1, $type, date('Y-m-d')]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
