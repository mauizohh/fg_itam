<?php

namespace App\Livewire\Administration;

use App\Models\Setting;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{
    public string $assetIdFormat = 'IT-[CATEGORY]-[YEAR]-[NUMBER]';

    public string $nextNumber = '000742';

    public string $defaultCurrency = 'MYR';

    public bool $warrantyAlert = true;

    public bool $overdueLoanAlert = true;

    public bool $auditDeadlineAlert = true;

    public bool $repairOverdueAlert = true;

    public string $lastSeenThreshold = '30 days';

    public string $auditFrequency = 'Annual';

    public string $requireSerial = 'Yes';

    public string $requireReturnInspection = 'Yes';

    public string $auditRetention = '7 years';

    public string $disposedRetention = 'Indefinite';

    public string $attachmentRetention = '7 years';

    public function mount(): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.settings'), 403);

        $this->assetIdFormat = Setting::get('asset_id_format', 'IT-[CATEGORY]-[YEAR]-[NUMBER]');
        $this->nextNumber = Setting::get('next_number', '000742');
        $this->defaultCurrency = Setting::get('default_currency', 'MYR');
    }

    public function saveSettings(): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.settings'), 403);

        Setting::set('asset_id_format', $this->assetIdFormat);
        Setting::set('next_number', $this->nextNumber);
        Setting::set('default_currency', $this->defaultCurrency);

        AuditLogService::record('Update settings', 'Settings', 'System', null, "Currency: {$this->defaultCurrency}, Next ID: {$this->nextNumber}");

        session()->flash('success', 'System settings saved successfully!');
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.settings'), 403);

        return view('livewire.administration.settings')
            ->layout('layouts.app', ['title' => 'Settings', 'breadcrumb' => 'IT Asset Management / Administration / Settings']);
    }
}
