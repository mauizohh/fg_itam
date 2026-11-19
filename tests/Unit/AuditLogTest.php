<?php

namespace Tests\Unit;

use App\Models\Asset;
use App\Models\AuditLog;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    public function test_clean_name_strips_parenthetical_and_suffix_positions(): void
    {
        $this->assertSame('Azril Employee', AuditLog::cleanName('Azril Employee (Test Staff)'));
        $this->assertSame('Amir Admin', AuditLog::cleanName('Amir Admin (System & IT Admin)'));
        $this->assertSame('Nora Support', AuditLog::cleanName('Nora Support - IT Support'));
        $this->assertSame('Sarah Lim', AuditLog::cleanName('Sarah Lim [Finance Manager]'));
        $this->assertSame('Daniel Wong', AuditLog::cleanName('Daniel Wong'));
        $this->assertSame('—', AuditLog::cleanName(''));
        $this->assertSame('—', AuditLog::cleanName(null));
    }

    public function test_resolve_employee_name_derives_correct_custodian(): void
    {
        $log1 = new AuditLog([
            'action' => 'Assign asset',
            'after_state' => 'Assigned to Sarah Lim',
        ]);
        $this->assertSame('Sarah Lim', $log1->resolveEmployeeName());

        $log2 = new AuditLog([
            'action' => 'Loan asset',
            'after_state' => 'Loaned to Ahmad Rahman (Due: 2026-12-31)',
        ]);
        $this->assertSame('Ahmad Rahman', $log2->resolveEmployeeName());

        $log3 = new AuditLog([
            'action' => 'Acknowledge asset',
            'after_state' => 'Acknowledged by Azril Employee (Test Staff)',
        ]);
        $this->assertSame('Azril Employee', $log3->resolveEmployeeName());

        $log4 = new AuditLog([
            'action' => 'Return asset',
            'before_state' => 'Assigned to Daniel Wong',
            'after_state' => 'Available',
        ]);
        $this->assertSame('Daniel Wong', $log4->resolveEmployeeName());

        $asset = new Asset([
            'assigned_user' => 'Sarah Lim (Finance)',
        ]);
        $log5 = new AuditLog([
            'action' => 'Update warranty',
            'before_state' => '2026-08-01',
            'after_state' => '2027-08-01',
        ]);
        $this->assertSame('Sarah Lim', $log5->resolveEmployeeName($asset));

        $log6 = new AuditLog([
            'action' => 'Asset created',
        ]);
        $this->assertSame('—', $log6->resolveEmployeeName());
    }

    public function test_scopes_can_be_invoked_without_bad_method_call(): void
    {
        $log = new AuditLog;
        $query = $log->newQuery();

        // Test standard Laravel scope call
        $query->forAssets();
        $this->assertStringContainsString('`object_type` = ?', $query->toSql());

        // Test scopeForAssets alias
        $query2 = $log->newQuery();
        $query2->scopeForAssets();
        $this->assertStringContainsString('`object_type` = ?', $query2->toSql());
    }
}
