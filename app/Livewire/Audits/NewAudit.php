<?php

namespace App\Livewire\Audits;

use App\Models\Asset;
use App\Models\Audit;
use App\Models\AuditException;
use App\Services\AuditLogService;
use Livewire\Component;

class NewAudit extends Component
{
    public string $scanInput = 'IT-LAP-2026-00123';
    public ?string $feedbackMessage = null;
    public string $feedbackType = 'success';

    public function recordResult(string $result)
    {
        $id = trim($this->scanInput);
        if (empty($id)) {
            $this->feedbackMessage = 'Please enter or scan an Asset ID!';
            $this->feedbackType = 'error';
            return;
        }

        $asset = Asset::find($id);

        if ($result === 'Found') {
            if ($asset) {
                $asset->update([
                    'verification_status' => 'Verified',
                    'last_physical_verification' => now(),
                    'last_verified_by' => 'Scanner Terminal 01',
                ]);
            }
            $this->feedbackMessage = "Asset {$id} verified successfully as FOUND!";
            $this->feedbackType = 'success';
            AuditLogService::record('Audit verification', 'Asset', $id, 'Unverified', 'Verified: Found');
        } else {
            // Discrepancy logged
            AuditException::create([
                'asset_id' => $id,
                'exception_type' => $result,
                'expected_info' => $asset ? ($asset->assigned_user . ' / ' . $asset->assigned_location) : 'KL Office',
                'observed_info' => ($result === 'Not Found') ? 'No physical scan' : ($result === 'Wrong Location' ? 'Observed in Engineering' : 'Hardware damaged observed'),
                'audit_id' => 'AUD-2026-006',
                'owner_department' => 'Service Desk',
                'status' => 'Open',
            ]);

            if ($asset) {
                $asset->update(['verification_status' => ($result === 'Not Found' ? 'Missing' : 'Review Required')]);
            }

            $this->feedbackMessage = "Discrepancy [{$result}] logged for Asset {$id}. Escalated to exception queue.";
            $this->feedbackType = 'warning';
            AuditLogService::record('Audit exception', 'Asset', $id, 'Verified', "Exception: {$result}");
        }

        // Reset or pick next sample asset
        $this->scanInput = 'IT-MON-2026-00391';
    }

    public function render()
    {
        $audit = Audit::find('AUD-2026-006') ?? new Audit([
            'id' => 'AUD-2026-006',
            'name' => '2026 Annual Laptop Audit',
            'scope' => 'Laptops - KL Office',
            'total_in_scope' => 420,
            'verified_count' => 260,
            'exceptions_count' => 11,
        ]);

        $remaining = max(0, $audit->total_in_scope - $audit->verified_count - $audit->exceptions_count);
        $percent = $audit->total_in_scope > 0 ? round(($audit->verified_count / $audit->total_in_scope) * 100) : 62;

        return view('livewire.audits.new-audit', compact('audit', 'remaining', 'percent'))
            ->layout('layouts.app', ['title' => 'New Audit - Verification Station', 'breadcrumb' => 'IT Asset Management / Audits / New Audit']);
    }
}
