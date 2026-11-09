<?php

namespace App\Livewire\Administration;

use App\Models\Setting;
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

    public function mount()
    {
        $this->assetIdFormat = Setting::get('asset_id_format', 'IT-[CATEGORY]-[YEAR]-[NUMBER]');
        $this->nextNumber = Setting::get('next_number', '000742');
        $this->defaultCurrency = Setting::get('default_currency', 'MYR');
    }

    public function saveSettings()
    {
        Setting::set('asset_id_format', $this->assetIdFormat);
        Setting::set('next_number', $this->nextNumber);
        Setting::set('default_currency', $this->defaultCurrency);

        session()->flash('success', 'System settings saved successfully!');
    }

    public function render()
    {
        return view('livewire.administration.settings')
            ->layout('layouts.app', ['title' => 'Settings', 'breadcrumb' => 'IT Asset Management / Administration / Settings']);
    }
}
