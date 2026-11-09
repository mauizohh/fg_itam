<?php

namespace App\Livewire\Reports;

use Livewire\Component;

class ReportsIndex extends Component
{
    public function render()
    {
        $reports = [
            [
                'id' => 'complete-register',
                'title' => 'Complete Asset Register',
                'description' => 'Current inventory with ownership and status',
                'icon' => 'document-text',
            ],
            [
                'id' => 'by-department',
                'title' => 'Assets by Department',
                'description' => 'Asset count and value by business unit',
                'icon' => 'office-building',
            ],
            [
                'id' => 'assignment-history',
                'title' => 'Asset Assignment History',
                'description' => 'Historical custody and transfers',
                'icon' => 'clock',
            ],
            [
                'id' => 'warranty-expiry',
                'title' => 'Warranty Expiry',
                'description' => 'Coverage expiring in selected period',
                'icon' => 'shield-check',
            ],
            [
                'id' => 'audit-exceptions',
                'title' => 'Audit Exceptions',
                'description' => 'Missing, damaged and location discrepancies',
                'icon' => 'exclamation-triangle',
            ],
            [
                'id' => 'license-utilization',
                'title' => 'License Utilization',
                'description' => 'Purchased vs assigned software licenses',
                'icon' => 'key',
            ],
        ];

        return view('livewire.reports.reports-index', compact('reports'))
            ->layout('layouts.app', ['title' => 'Reports', 'breadcrumb' => 'IT Asset Management / Reports']);
    }
}
