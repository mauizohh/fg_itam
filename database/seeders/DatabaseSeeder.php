<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categories = [
            ['id' => 'LAP', 'name' => 'Laptop', 'prefix' => 'IT-LAP', 'useful_life_years' => 4, 'tracking_fields' => 'Serial, hostname', 'status' => 'Active'],
            ['id' => 'DSK', 'name' => 'Desktop', 'prefix' => 'IT-DSK', 'useful_life_years' => 5, 'tracking_fields' => 'Serial, hostname', 'status' => 'Active'],
            ['id' => 'MON', 'name' => 'Monitor', 'prefix' => 'IT-MON', 'useful_life_years' => 5, 'tracking_fields' => 'Serial', 'status' => 'Active'],
            ['id' => 'MOB', 'name' => 'Mobile Device', 'prefix' => 'IT-MOB', 'useful_life_years' => 3, 'tracking_fields' => 'Serial, IMEI', 'status' => 'Active'],
            ['id' => 'NET', 'name' => 'Network Equipment', 'prefix' => 'IT-NET', 'useful_life_years' => 5, 'tracking_fields' => 'Serial, MAC, IP', 'status' => 'Active'],
            ['id' => 'SRV', 'name' => 'Server', 'prefix' => 'IT-SRV', 'useful_life_years' => 5, 'tracking_fields' => 'Serial, hostname, IP', 'status' => 'Active'],
            ['id' => 'PRN', 'name' => 'Printer', 'prefix' => 'IT-PRN', 'useful_life_years' => 4, 'tracking_fields' => 'Serial, IP', 'status' => 'Active'],
            ['id' => 'ACC', 'name' => 'Accessory', 'prefix' => 'IT-ACC', 'useful_life_years' => 2, 'tracking_fields' => 'Serial', 'status' => 'Active'],
        ];
        foreach ($categories as $cat) {
            DB::table('fg_itam_categories')->updateOrInsert(['id' => $cat['id']], array_merge($cat, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 2. Locations
        $locations = [
            ['country' => 'Malaysia', 'office' => 'Kuala Lumpur Office', 'area' => 'Level 5 - Finance', 'owner_department' => 'Finance', 'assets_count' => 92],
            ['country' => 'Malaysia', 'office' => 'Kuala Lumpur Office', 'area' => 'Level 6 - Engineering', 'owner_department' => 'Engineering', 'assets_count' => 184],
            ['country' => 'Malaysia', 'office' => 'Kuala Lumpur Office', 'area' => 'IT Store', 'owner_department' => 'IT Operations', 'assets_count' => 73],
            ['country' => 'Malaysia', 'office' => 'Kuala Lumpur Office', 'area' => 'Server Room', 'owner_department' => 'Infrastructure', 'assets_count' => 64],
            ['country' => 'Malaysia', 'office' => 'Penang Office', 'area' => 'Main Office - Level 2', 'owner_department' => 'Sales', 'assets_count' => 120],
        ];
        foreach ($locations as $loc) {
            DB::table('fg_itam_locations')->updateOrInsert(
                ['office' => $loc['office'], 'area' => $loc['area']],
                array_merge($loc, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        // 3. Departments
        $departments = [
            ['name' => 'Finance', 'owner_title' => 'CFO', 'active_assets_count' => 92, 'available_count' => 4, 'repair_count' => 1, 'missing_count' => 0, 'purchase_value' => 284000.00, 'exceptions_count' => 2],
            ['name' => 'Engineering', 'owner_title' => 'CTO', 'active_assets_count' => 184, 'available_count' => 12, 'repair_count' => 5, 'missing_count' => 1, 'purchase_value' => 812000.00, 'exceptions_count' => 4],
            ['name' => 'Sales', 'owner_title' => 'Sales Director', 'active_assets_count' => 138, 'available_count' => 6, 'repair_count' => 2, 'missing_count' => 0, 'purchase_value' => 491000.00, 'exceptions_count' => 1],
            ['name' => 'Operations', 'owner_title' => 'COO', 'active_assets_count' => 216, 'available_count' => 8, 'repair_count' => 4, 'missing_count' => 2, 'purchase_value' => 703000.00, 'exceptions_count' => 3],
        ];
        foreach ($departments as $dept) {
            DB::table('fg_itam_departments')->updateOrInsert(['name' => $dept['name']], array_merge($dept, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 4. Employees
        $employees = [
            ['employee_id' => 'EMP-0101', 'name' => 'Sarah Lim', 'email' => 'sarah.lim@firstgrade.com', 'department' => 'Finance', 'location' => 'KL Office', 'assigned_assets_count' => 3, 'pending_returns_count' => 0, 'exceptions_count' => 0, 'asset_value' => 8900.00, 'status' => 'Active'],
            ['employee_id' => 'EMP-0102', 'name' => 'Daniel Wong', 'email' => 'daniel.wong@firstgrade.com', 'department' => 'Engineering', 'location' => 'KL Office', 'assigned_assets_count' => 4, 'pending_returns_count' => 0, 'exceptions_count' => 0, 'asset_value' => 14200.00, 'status' => 'Active'],
            ['employee_id' => 'EMP-0103', 'name' => 'Aisha Noor', 'email' => 'aisha.noor@firstgrade.com', 'department' => 'Sales', 'location' => 'Penang Office', 'assigned_assets_count' => 2, 'pending_returns_count' => 1, 'exceptions_count' => 1, 'asset_value' => 6100.00, 'status' => 'Active'],
            ['employee_id' => 'EMP-0104', 'name' => 'Ahmad Rahman', 'email' => 'ahmad.rahman@firstgrade.com', 'department' => 'Operations', 'location' => 'KL Office', 'assigned_assets_count' => 5, 'pending_returns_count' => 0, 'exceptions_count' => 0, 'asset_value' => 11500.00, 'status' => 'Active'],
            ['employee_id' => 'EMP-0001', 'name' => 'Amir Admin', 'email' => 'amir@example.com', 'department' => 'IT Operations', 'location' => 'KL Office', 'assigned_assets_count' => 2, 'pending_returns_count' => 0, 'exceptions_count' => 0, 'asset_value' => 9000.00, 'status' => 'Active'],
            ['employee_id' => 'EMP-0002', 'name' => 'Nora Support', 'email' => 'nora@example.com', 'department' => 'IT Support', 'location' => 'KL Office', 'assigned_assets_count' => 1, 'pending_returns_count' => 0, 'exceptions_count' => 0, 'asset_value' => 4500.00, 'status' => 'Active'],
            ['employee_id' => 'EMP-0003', 'name' => 'Lee Finance', 'email' => 'lee@example.com', 'department' => 'Finance', 'location' => 'KL Office', 'assigned_assets_count' => 1, 'pending_returns_count' => 0, 'exceptions_count' => 0, 'asset_value' => 5200.00, 'status' => 'Active'],
        ];
        foreach ($employees as $emp) {
            DB::table('fg_itam_employees')->updateOrInsert(['name' => $emp['name']], array_merge($emp, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 5. Suppliers
        $suppliers = [
            ['name' => 'TechSource Sdn Bhd', 'category' => 'Hardware', 'contact_email' => 'sales@techsource.example', 'contact_phone' => '+60 3-8000 1122', 'open_pos_count' => 3, 'ytd_spend' => 428000.00, 'status' => 'Active'],
            ['name' => 'NetWorks Supply', 'category' => 'Networking', 'contact_email' => 'orders@networks.example', 'contact_phone' => '+60 3-8000 3344', 'open_pos_count' => 1, 'ytd_spend' => 196500.00, 'status' => 'Active'],
            ['name' => 'Office Device Co.', 'category' => 'Printers', 'contact_email' => 'sales@office.example', 'contact_phone' => '+60 3-8000 5566', 'open_pos_count' => 0, 'ytd_spend' => 82300.00, 'status' => 'Active'],
        ];
        foreach ($suppliers as $sup) {
            DB::table('fg_itam_suppliers')->updateOrInsert(['name' => $sup['name']], array_merge($sup, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 6. Purchases
        $purchases = [
            ['id' => 'PUR-2026-0148', 'supplier_name' => 'TechSource', 'invoice_number' => 'INV-88301', 'purchase_date' => '2026-08-12', 'amount' => 54000.00, 'assets_description' => '12 laptops', 'status' => 'Received'],
            ['id' => 'PUR-2026-0142', 'supplier_name' => 'NetWorks Supply', 'invoice_number' => 'INV-23119', 'purchase_date' => '2026-08-05', 'amount' => 31800.00, 'assets_description' => '6 access points', 'status' => 'Registered'],
            ['id' => 'PUR-2026-0138', 'supplier_name' => 'Office Device Co.', 'invoice_number' => 'INV-55102', 'purchase_date' => '2026-07-29', 'amount' => 12400.00, 'assets_description' => '2 printers', 'status' => 'Registered'],
        ];
        foreach ($purchases as $p) {
            DB::table('fg_itam_purchases')->updateOrInsert(['id' => $p['id']], array_merge($p, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 7. Purchase Orders
        $pos = [
            ['id' => 'PO-2026-0088', 'supplier_name' => 'TechSource', 'requested_by' => 'IT Operations', 'po_date' => '2026-08-11', 'amount' => 72500.00, 'delivery_date' => '2026-08-20', 'status' => 'Approved'],
            ['id' => 'PO-2026-0084', 'supplier_name' => 'NetWorks Supply', 'requested_by' => 'Infrastructure', 'po_date' => '2026-08-07', 'amount' => 48900.00, 'delivery_date' => '2026-08-18', 'status' => 'Ordered'],
            ['id' => 'PO-2026-0079', 'supplier_name' => 'TechSource', 'requested_by' => 'Service Desk', 'po_date' => '2026-07-29', 'amount' => 18600.00, 'delivery_date' => '2026-08-15', 'status' => 'Partial'],
        ];
        foreach ($pos as $po) {
            DB::table('fg_itam_purchase_orders')->updateOrInsert(['id' => $po['id']], array_merge($po, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 8. Repairs
        $repairs = [
            ['id' => 'RPR-0261', 'asset_id' => 'IT-LAP-2026-00288', 'issue' => 'Battery failure', 'opened_date' => '2026-08-08', 'target_return_date' => '2026-08-15', 'vendor' => 'TechCare', 'cost' => 420.00, 'status' => 'Repairing'],
            ['id' => 'RPR-0254', 'asset_id' => 'IT-PRN-2026-00042', 'issue' => 'Paper feed', 'opened_date' => '2026-08-05', 'target_return_date' => '2026-08-12', 'vendor' => 'PrintWorks', 'cost' => 280.00, 'status' => 'Overdue'],
            ['id' => 'RPR-0268', 'asset_id' => 'IT-LAP-2026-00311', 'issue' => 'Screen damage', 'opened_date' => '2026-08-11', 'target_return_date' => '2026-08-18', 'vendor' => 'TechCare', 'cost' => 690.00, 'status' => 'Awaiting Part'],
        ];
        foreach ($repairs as $rpr) {
            DB::table('fg_itam_repairs')->updateOrInsert(['id' => $rpr['id']], array_merge($rpr, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 9. Software
        $softwareList = [
            ['name' => 'Productivity Suite', 'vendor' => 'Cloud Vendor', 'type' => 'Subscription', 'managed_devices_count' => 420, 'owner_department' => 'IT Operations', 'status' => 'Active'],
            ['name' => 'Design Suite', 'vendor' => 'Creative Vendor', 'type' => 'Subscription', 'managed_devices_count' => 32, 'owner_department' => 'Marketing', 'status' => 'Active'],
            ['name' => 'Endpoint Security', 'vendor' => 'Security Vendor', 'type' => 'Subscription', 'managed_devices_count' => 510, 'owner_department' => 'Security', 'status' => 'Active'],
            ['name' => 'CAD Professional', 'vendor' => 'Engineering Vendor', 'type' => 'Perpetual', 'managed_devices_count' => 18, 'owner_department' => 'Engineering', 'status' => 'Active'],
        ];
        foreach ($softwareList as $soft) {
            DB::table('fg_itam_software')->updateOrInsert(['name' => $soft['name']], array_merge($soft, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 10. Licenses
        $licenses = [
            ['software_name' => 'Productivity Suite', 'purchased_seats' => 500, 'assigned_seats' => 452, 'available_seats' => 48, 'utilization_percent' => 90, 'status' => 'Healthy'],
            ['software_name' => 'Design Suite', 'purchased_seats' => 40, 'assigned_seats' => 32, 'available_seats' => 8, 'utilization_percent' => 80, 'status' => 'Healthy'],
            ['software_name' => 'Endpoint Security', 'purchased_seats' => 520, 'assigned_seats' => 510, 'available_seats' => 10, 'utilization_percent' => 98, 'status' => 'Review'],
            ['software_name' => 'CAD Professional', 'purchased_seats' => 20, 'assigned_seats' => 18, 'available_seats' => 2, 'utilization_percent' => 90, 'status' => 'Healthy'],
        ];
        foreach ($licenses as $lic) {
            DB::table('fg_itam_licenses')->updateOrInsert(['software_name' => $lic['software_name']], array_merge($lic, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 11. Renewals
        $renewals = [
            ['id' => 'REN-0091', 'service_name' => 'Endpoint Security', 'owner' => 'Security', 'renewal_date' => '2026-08-28', 'annual_cost' => 48000.00, 'status' => 'Approval Needed'],
            ['id' => 'REN-0092', 'service_name' => 'Cloud Backup', 'owner' => 'Infrastructure', 'renewal_date' => '2026-09-12', 'annual_cost' => 36500.00, 'status' => 'In Review'],
            ['id' => 'REN-0094', 'service_name' => 'Design Suite', 'owner' => 'Marketing', 'renewal_date' => '2026-10-01', 'annual_cost' => 28800.00, 'status' => 'Planned'],
            ['id' => 'REN-0098', 'service_name' => 'Domain Portfolio', 'owner' => 'IT Operations', 'renewal_date' => '2026-10-18', 'annual_cost' => 4200.00, 'status' => 'Planned'],
        ];
        foreach ($renewals as $ren) {
            DB::table('fg_itam_renewals')->updateOrInsert(['id' => $ren['id']], array_merge($ren, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 12. Audits
        $audits = [
            ['id' => 'AUD-2026-006', 'name' => '2026 Annual Laptop Audit', 'scope' => 'KL Laptop Audit', 'period' => 'Aug 2026', 'total_in_scope' => 420, 'verified_count' => 260, 'exceptions_count' => 11, 'accuracy_rate' => null, 'status' => 'In Progress'],
            ['id' => 'AUD-2026-004', 'name' => 'Infrastructure Q2 Audit', 'scope' => 'Network Equipment', 'period' => 'Jun 2026', 'total_in_scope' => 164, 'verified_count' => 164, 'exceptions_count' => 2, 'accuracy_rate' => 98.8, 'status' => 'Completed'],
            ['id' => 'AUD-2026-002', 'name' => 'Penang Regional Verification', 'scope' => 'Penang Office', 'period' => 'Mar 2026', 'total_in_scope' => 240, 'verified_count' => 238, 'exceptions_count' => 4, 'accuracy_rate' => 98.3, 'status' => 'Completed'],
        ];
        foreach ($audits as $aud) {
            DB::table('fg_itam_audits')->updateOrInsert(['id' => $aud['id']], array_merge($aud, ['updated_at' => now(), 'created_at' => now()]));
        }

        // 13. Audit Exceptions
        $exceptions = [
            ['asset_id' => 'IT-LAP-2026-00381', 'exception_type' => 'Not Found', 'expected_info' => 'Sarah Lim / KL', 'observed_info' => 'No scan', 'audit_id' => 'AUD-2026-006', 'owner_department' => 'Service Desk', 'status' => 'Open'],
            ['asset_id' => 'IT-MON-2026-00118', 'exception_type' => 'Wrong Location', 'expected_info' => 'Finance', 'observed_info' => 'Engineering', 'audit_id' => 'AUD-2026-006', 'owner_department' => 'IT Ops', 'status' => 'Open'],
            ['asset_id' => 'IT-LAP-2026-00404', 'exception_type' => 'Damaged', 'expected_info' => 'Good', 'observed_info' => 'Cracked screen', 'audit_id' => 'AUD-2026-006', 'owner_department' => 'Service Desk', 'status' => 'Open'],
            ['asset_id' => 'IT-MOB-2026-00022', 'exception_type' => 'Wrong User', 'expected_info' => 'Aisha Noor', 'observed_info' => 'Unassigned', 'audit_id' => 'AUD-2026-002', 'owner_department' => 'IT Ops', 'status' => 'Open'],
        ];
        foreach ($exceptions as $ex) {
            DB::table('fg_itam_audit_exceptions')->updateOrInsert(
                ['asset_id' => $ex['asset_id'], 'audit_id' => $ex['audit_id']],
                array_merge($ex, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        // 14. Master Assets (fg_itam_assets)
        // All following the exact required format: IT-DEVICE(3 ALPHABET)-YEAR-NUMBER 5 series
        $assets = [
            // Helpdesk connected asset
            [
                'id' => 'IT-DSK-2026-00001',
                'asset_tag' => 'TAG-DSK-00001',
                'name' => 'HP ProDesk 4 Tower G1',
                'category_id' => 'DSK',
                'category_name' => 'Desktop',
                'brand' => 'HP',
                'model' => 'ProDesk 4 Tower G1',
                'serial_number' => 'DT-HP-057',
                'status' => 'Assigned',
                'hostname' => 'SAF-DSK-001',
                'ip_address' => '192.168.10.45',
                'mac_address' => '00:14:22:01:23:45',
                'department' => 'Safety',
                'assigned_user' => 'IZHAM MOHAMAD NAYAN',
                'assigned_location' => 'KL Office - Level 4',
                'purchase_date' => '2025-10-08',
                'purchase_cost' => 3800.00,
                'warranty_end' => '2026-10-08',
                'condition' => 'Good',
                'verification_status' => 'Verified',
                'last_seen_source' => 'Endpoint inventory',
                'last_electronically_seen' => '2026-11-08 14:20:00',
                'last_physical_verification' => '2026-08-10',
            ],
            // Key UI assets
            [
                'id' => 'IT-LAP-2026-00123',
                'asset_tag' => 'TAG-LAP-00123',
                'name' => 'Dell Latitude 7450',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 7450',
                'serial_number' => 'DL7450-93821',
                'status' => 'Assigned',
                'hostname' => 'FIN-LT-023',
                'ip_address' => '192.168.12.88',
                'mac_address' => 'F8:E4:E3:44:91:22',
                'department' => 'Finance',
                'assigned_user' => 'Sarah Lim',
                'assigned_location' => 'KL Office - Level 5',
                'assigned_date' => '2026-08-10',
                'purchase_date' => '2026-08-01',
                'purchase_cost' => 5200.00,
                'warranty_start' => '2026-08-01',
                'warranty_end' => '2029-08-01',
                'replacement_due' => 'Aug 2030',
                'warranty_provider' => 'Dell',
                'condition' => 'Good',
                'verification_status' => 'Verified',
                'last_seen_source' => 'Endpoint inventory',
                'last_electronically_seen' => '2026-08-10 16:42:00',
                'last_physical_verification' => '2026-06-15',
                'last_verified_by' => 'IT Ops',
                'accessories' => 'Charger, Dock',
                'remarks' => 'Standard employee laptop assignment',
            ],
            [
                'id' => 'IT-MON-2026-00391',
                'asset_tag' => 'TAG-MON-00391',
                'name' => 'Dell 27 Monitor',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'UltraSharp 27',
                'serial_number' => 'MN27-202942',
                'status' => 'Assigned',
                'department' => 'Finance',
                'assigned_user' => 'Sarah Lim',
                'assigned_location' => 'KL Office - Level 5',
                'assigned_date' => '2026-08-10',
                'purchase_cost' => 1800.00,
                'condition' => 'Good',
                'verification_status' => 'Verified',
                'last_physical_verification' => '2026-06-15',
            ],
            [
                'id' => 'IT-ACC-2026-00678',
                'asset_tag' => 'TAG-ACC-00678',
                'name' => 'USB-C Dock',
                'category_id' => 'ACC',
                'category_name' => 'Accessory',
                'brand' => 'Dell',
                'model' => 'WD19S Dock',
                'serial_number' => 'WD19S-883192',
                'status' => 'Assigned',
                'department' => 'Finance',
                'assigned_user' => 'Sarah Lim',
                'assigned_location' => 'KL Office - Level 5',
                'assigned_date' => '2026-08-10',
                'purchase_cost' => 900.00,
                'condition' => 'Good',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-LAP-2026-00418',
                'asset_tag' => 'TAG-LAP-00418',
                'name' => 'Lenovo ThinkPad T14',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad T14',
                'serial_number' => 'T14-882103',
                'status' => 'Available',
                'assigned_location' => 'IT Store',
                'purchase_cost' => 4800.00,
                'condition' => 'Good',
                'verification_status' => 'Verified',
                'last_seen_source' => 'IT Store Stocktake',
                'last_physical_verification' => '2026-08-14',
            ],
            [
                'id' => 'IT-MON-2026-00601',
                'asset_tag' => 'TAG-MON-00601',
                'name' => 'Dell P2723QE Monitor',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'Dell P2723QE',
                'serial_number' => 'MN-DL-99120',
                'status' => 'Available',
                'assigned_location' => 'IT Store',
                'purchase_cost' => 1950.00,
                'condition' => 'New',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-MOB-2026-00112',
                'asset_tag' => 'TAG-MOB-00112',
                'name' => 'iPhone 15',
                'category_id' => 'MOB',
                'category_name' => 'Mobile Device',
                'brand' => 'Apple',
                'model' => 'iPhone 15 128GB',
                'serial_number' => 'IP15-992384',
                'imei' => '358291039481920',
                'status' => 'Available',
                'assigned_location' => 'IT Store',
                'purchase_cost' => 3899.00,
                'condition' => 'Good',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-NET-2026-00303',
                'asset_tag' => 'TAG-NET-00303',
                'name' => 'Aruba Access Point AP-635',
                'category_id' => 'NET',
                'category_name' => 'Network Equipment',
                'brand' => 'Aruba',
                'model' => 'AP-635',
                'serial_number' => 'AP635-449102',
                'mac_address' => '00:1A:2B:3C:4D:5E',
                'status' => 'Available',
                'assigned_location' => 'IT Store',
                'purchase_cost' => 2400.00,
                'condition' => 'New',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-LAP-2026-00088',
                'asset_tag' => 'TAG-LAP-00088',
                'name' => 'MacBook Pro 14',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Apple',
                'model' => 'MacBook Pro 14 M3',
                'serial_number' => 'MBP-M3-44109',
                'status' => 'Assigned',
                'department' => 'Engineering',
                'assigned_user' => 'Daniel Wong',
                'assigned_location' => 'KL Office - Level 6',
                'assigned_date' => '2026-07-02',
                'purchase_cost' => 8400.00,
                'condition' => 'Good',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-MOB-2026-00071',
                'asset_tag' => 'TAG-MOB-00071',
                'name' => 'Samsung S24',
                'category_id' => 'MOB',
                'category_name' => 'Mobile Device',
                'brand' => 'Samsung',
                'model' => 'Galaxy S24',
                'serial_number' => 'SS-S24-11948',
                'imei' => '359182049182390',
                'status' => 'Assigned',
                'department' => 'Sales',
                'assigned_user' => 'Aisha Noor',
                'assigned_location' => 'Penang Office',
                'assigned_date' => '2026-06-16',
                'purchase_cost' => 3699.00,
                'condition' => 'Good',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-NET-2026-00201',
                'asset_tag' => 'TAG-NET-00201',
                'name' => 'Cisco Switch 48P',
                'category_id' => 'NET',
                'category_name' => 'Network Equipment',
                'brand' => 'Cisco',
                'model' => 'Catalyst 9300 48P',
                'serial_number' => 'CS48-338012',
                'ip_address' => '192.168.10.2',
                'mac_address' => 'CC:D5:39:10:88:21',
                'status' => 'Assigned',
                'department' => 'Engineering',
                'assigned_user' => 'Network Team',
                'assigned_location' => 'Server Room',
                'purchase_cost' => 14500.00,
                'warranty_end' => '2026-08-29',
                'warranty_provider' => 'Vendor Support',
                'condition' => 'Good',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-LAP-2026-00288',
                'asset_tag' => 'TAG-LAP-00288',
                'name' => 'HP EliteBook 840',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'HP',
                'model' => 'EliteBook 840 G10',
                'serial_number' => 'HP840-772013',
                'status' => 'In Repair',
                'assigned_location' => 'Repair Vendor',
                'purchase_cost' => 4900.00,
                'condition' => 'Damaged',
                'verification_status' => 'Review Required',
            ],
            [
                'id' => 'IT-PRN-2026-00042',
                'asset_tag' => 'TAG-PRN-00042',
                'name' => 'HP LaserJet',
                'category_id' => 'PRN',
                'category_name' => 'Printer',
                'brand' => 'HP',
                'model' => 'LaserJet Enterprise M608',
                'serial_number' => 'HPLJ-339102',
                'status' => 'In Repair',
                'assigned_location' => 'Repair Vendor',
                'purchase_cost' => 3400.00,
                'condition' => 'Damaged',
                'verification_status' => 'Review Required',
            ],
            [
                'id' => 'IT-LAP-2026-00311',
                'asset_tag' => 'TAG-LAP-00311',
                'name' => 'Dell Latitude 5430',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 5430',
                'serial_number' => 'DL-311094',
                'status' => 'In Repair',
                'assigned_location' => 'Repair Vendor',
                'purchase_cost' => 4500.00,
                'condition' => 'Damaged',
                'verification_status' => 'Review Required',
            ],
            [
                'id' => 'IT-SRV-2026-00045',
                'asset_tag' => 'TAG-SRV-00045',
                'name' => 'Rack Server PowerEdge R750',
                'category_id' => 'SRV',
                'category_name' => 'Server',
                'brand' => 'Dell',
                'model' => 'PowerEdge R750',
                'serial_number' => 'RCK-SRV-99104',
                'ip_address' => '192.168.1.10',
                'status' => 'Assigned',
                'department' => 'Infrastructure',
                'assigned_location' => 'Server Room',
                'purchase_cost' => 28500.00,
                'warranty_end' => '2026-09-10',
                'warranty_provider' => 'Vendor Support',
                'condition' => 'Good',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-LAP-2026-00102',
                'asset_tag' => 'TAG-LAP-00102',
                'name' => 'Dell Latitude 7450',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 7450',
                'serial_number' => 'DL-102941',
                'status' => 'Assigned',
                'department' => 'Finance',
                'assigned_user' => 'Lee Finance',
                'assigned_location' => 'KL Office - Level 5',
                'purchase_cost' => 5200.00,
                'warranty_end' => '2026-08-24',
                'warranty_provider' => 'Dell',
                'condition' => 'Good',
                'verification_status' => 'Verified',
            ],
            // Retired Assets
            [
                'id' => 'IT-LAP-2026-00019',
                'asset_tag' => 'TAG-LAP-00019',
                'name' => 'Dell Latitude 5490',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 5490',
                'serial_number' => 'DL5490-10928',
                'status' => 'Retired',
                'retired_date' => '2026-07-31',
                'retirement_reason' => 'End of life',
                'disposition' => 'Recycle',
                'data_sanitization' => 'Verified',
                'condition' => 'Fair',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-SRV-2026-00007',
                'asset_tag' => 'TAG-SRV-00007',
                'name' => 'Dell PowerEdge R720',
                'category_id' => 'SRV',
                'category_name' => 'Server',
                'brand' => 'Dell',
                'model' => 'PowerEdge R720',
                'serial_number' => 'PE-R720-9912',
                'status' => 'Retired',
                'retired_date' => '2026-06-12',
                'retirement_reason' => 'Obsolete',
                'disposition' => 'Destroy',
                'data_sanitization' => 'Verified',
                'condition' => 'Defective',
                'verification_status' => 'Verified',
            ],
            [
                'id' => 'IT-MON-2026-00031',
                'asset_tag' => 'TAG-MON-00031',
                'name' => 'Dell 24 Monitor',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'UltraSharp 24',
                'serial_number' => 'MN24-88129',
                'status' => 'Retired',
                'retired_date' => '2026-05-23',
                'retirement_reason' => 'Beyond repair',
                'disposition' => 'Recycle',
                'data_sanitization' => 'N/A',
                'condition' => 'Damaged',
                'verification_status' => 'Verified',
            ],
            // Exception items
            [
                'id' => 'IT-LAP-2026-00381',
                'asset_tag' => 'TAG-LAP-00381',
                'name' => 'Dell Latitude 7320',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 7320',
                'serial_number' => 'DL7320-99412',
                'status' => 'Assigned',
                'department' => 'Finance',
                'assigned_user' => 'Sarah Lim',
                'assigned_location' => 'KL Office',
                'verification_status' => 'Missing',
            ],
            [
                'id' => 'IT-MON-2026-00118',
                'asset_tag' => 'TAG-MON-00118',
                'name' => 'Dell 27 Monitor',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'P2720D',
                'serial_number' => 'MN27-11829',
                'status' => 'Assigned',
                'department' => 'Finance',
                'assigned_location' => 'KL Office - Engineering',
                'verification_status' => 'Review Required',
            ],
            [
                'id' => 'IT-LAP-2026-00404',
                'asset_tag' => 'TAG-LAP-00404',
                'name' => 'Lenovo ThinkPad L14',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad L14 Gen 4',
                'serial_number' => 'TP-L14-40401',
                'status' => 'Assigned',
                'department' => 'Service Desk',
                'assigned_location' => 'KL Office',
                'condition' => 'Damaged',
                'verification_status' => 'Review Required',
            ],
            [
                'id' => 'IT-MOB-2026-00022',
                'asset_tag' => 'TAG-MOB-00022',
                'name' => 'Samsung Galaxy A54',
                'category_id' => 'MOB',
                'category_name' => 'Mobile Device',
                'brand' => 'Samsung',
                'model' => 'Galaxy A54',
                'serial_number' => 'SM-A54-22019',
                'imei' => '359910294819201',
                'status' => 'Available',
                'assigned_location' => 'Penang Office',
                'verification_status' => 'Review Required',
            ],
        ];

        foreach ($assets as $asset) {
            DB::table('fg_itam_assets')->updateOrInsert(['id' => $asset['id']], array_merge($asset, ['updated_at' => now(), 'created_at' => now()]));

            // Also synchronize with fg_helpdesk_assets to preserve cross-system link
            if (\Illuminate\Support\Facades\Schema::hasTable('fg_helpdesk_assets')) {
                DB::table('fg_helpdesk_assets')->updateOrInsert(
                    ['id' => $asset['id']],
                    [
                        'name' => $asset['name'],
                        'assigned' => !empty($asset['assigned_user']) ? ($asset['assigned_user'] . (!empty($asset['department']) ? ' (' . $asset['department'] . ')' : '')) : 'Unassigned',
                        'assigned_user' => $asset['assigned_user'] ?? '',
                        'assigned_department' => $asset['department'] ?? '',
                        'serial' => $asset['serial_number'] ?? '',
                        'purchase' => $asset['purchase_date'] ?? date('Y-m-d'),
                        'warranty' => $asset['warranty_end'] ?? date('Y-m-d', strtotime('+3 years')),
                        'status' => $asset['status'] ?? 'Available',
                        'created_at' => now(),
                    ]
                );
            }
        }

        // 15. Audit Activity Logs
        $logs = [
            ['timestamp' => Carbon::now()->subMinutes(18), 'user' => 'Amir Admin', 'action' => 'Assign asset', 'object_type' => 'Asset', 'object_id' => 'IT-LAP-2026-00123', 'before_state' => 'Available', 'after_state' => 'Assigned to Sarah Lim'],
            ['timestamp' => Carbon::now()->subHours(1)->subMinutes(42), 'user' => 'Nora Support', 'action' => 'Return asset', 'object_type' => 'Asset', 'object_id' => 'IT-MON-2026-00391', 'before_state' => 'Assigned', 'after_state' => 'Available'],
            ['timestamp' => Carbon::now()->subHours(2), 'user' => 'Amir Admin', 'action' => 'Update warranty', 'object_type' => 'Asset', 'object_id' => 'IT-NET-2026-00201', 'before_state' => '29 Aug 2026', 'after_state' => '29 Aug 2027'],
            ['timestamp' => Carbon::now()->subDay()->subHours(4), 'user' => 'Nora Support', 'action' => 'Open repair', 'object_type' => 'Asset', 'object_id' => 'IT-LAP-2026-00288', 'before_state' => 'Assigned', 'after_state' => 'In Repair'],
            ['timestamp' => Carbon::now()->subDay()->subHours(6), 'user' => 'System', 'action' => 'Threshold alert', 'object_type' => 'Asset', 'object_id' => 'IT-LAP-2026-00381', 'before_state' => 'Last seen 29 days', 'after_state' => 'Last seen 30 days'],
        ];
        foreach ($logs as $log) {
            DB::table('fg_itam_audit_logs')->insert(array_merge($log, ['created_at' => now(), 'updated_at' => now()]));
        }

        // 16. System Settings
        $settings = [
            'asset_id_format' => 'IT-[CATEGORY]-[YEAR]-[NUMBER]',
            'next_number' => '000742',
            'default_currency' => 'MYR',
            'last_seen_threshold_days' => '30',
            'physical_audit_frequency' => 'Annual',
            'require_serial_number' => '1',
            'require_return_inspection' => '1',
            'alert_warranty_expiry' => '1',
            'alert_overdue_loan' => '1',
            'alert_audit_deadline' => '1',
            'alert_repair_overdue' => '1',
            'audit_log_retention_years' => '7',
            'disposed_asset_retention' => 'Indefinite',
        ];
        foreach ($settings as $k => $v) {
            DB::table('fg_itam_settings')->updateOrInsert(['key' => $k], ['value' => $v, 'updated_at' => now(), 'created_at' => now()]);
        }
    }
}
