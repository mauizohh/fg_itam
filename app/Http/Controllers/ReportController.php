<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditException;
use App\Models\Department;
use App\Models\License;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function downloadPdf(string $type)
    {
        $date = now()->format('d M Y');

        switch ($type) {
            case 'complete-register':
                $title = 'Complete Asset Register';
                $data = Asset::orderBy('id')->get();
                $view = 'reports.pdf.complete-register';
                break;

            case 'by-department':
                $title = 'Assets by Department Report';
                $departments = Department::all();
                $assetsByDept = Asset::whereNotNull('department')->get()->groupBy('department');
                $data = compact('departments', 'assetsByDept');
                $view = 'reports.pdf.by-department';
                break;

            case 'assignment-history':
                $title = 'Asset Assignment & Transfer History';
                $data = Transaction::orderByDesc('created_at')->limit(100)->get();
                $view = 'reports.pdf.assignment-history';
                break;

            case 'warranty-expiry':
                $title = 'Upcoming Warranty Expirations';
                $data = Asset::whereNotNull('warranty_end')->orderBy('warranty_end')->limit(50)->get();
                $view = 'reports.pdf.warranty-expiry';
                break;

            case 'audit-exceptions':
                $title = 'Audit Exceptions & Discrepancies';
                $data = AuditException::orderByDesc('created_at')->get();
                $view = 'reports.pdf.audit-exceptions';
                break;

            case 'license-utilization':
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
        ])->setPaper('a4', 'landscape');

        return $pdf->download(str_replace(' ', '_', strtolower($title)) . '_' . date('Ymd') . '.pdf');
    }

    public function exportCsv(string $type)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$type}_" . date('Ymd') . ".csv\"",
        ];

        $callback = function () use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'complete-register') {
                fputcsv($handle, ['Asset ID', 'Name', 'Category', 'Serial', 'Assigned To', 'Location', 'Status', 'Cost (MYR)', 'Warranty End']);
                foreach (Asset::all() as $a) {
                    fputcsv($handle, [$a->id, $a->name, $a->category_name, $a->serial_number, $a->assigned_user, $a->assigned_location, $a->status, $a->purchase_cost, $a->warranty_end]);
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
