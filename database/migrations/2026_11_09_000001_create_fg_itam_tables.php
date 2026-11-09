<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categories
        if (!Schema::hasTable('fg_itam_categories')) {
            Schema::create('fg_itam_categories', function (Blueprint $table) {
                $table->string('id', 10)->primary(); // LAP, DSK, MON, MOB, SRV, NET, PRN, ACC
                $table->string('name', 100);
                $table->string('prefix', 15); // IT-LAP, IT-DSK
                $table->integer('useful_life_years')->default(4);
                $table->string('tracking_fields', 255)->default('Serial number');
                $table->string('status', 20)->default('Active');
                $table->timestamps();
            });
        }

        // 2. Locations
        if (!Schema::hasTable('fg_itam_locations')) {
            Schema::create('fg_itam_locations', function (Blueprint $table) {
                $table->id();
                $table->string('country', 100)->default('Malaysia');
                $table->string('office', 150); // Kuala Lumpur Office, Penang Office
                $table->string('area', 150); // Level 5 - Finance, IT Store, Server Room
                $table->string('owner_department', 100)->nullable();
                $table->integer('assets_count')->default(0);
                $table->timestamps();
            });
        }

        // 3. Departments
        if (!Schema::hasTable('fg_itam_departments')) {
            Schema::create('fg_itam_departments', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('owner_title', 100)->nullable(); // CFO, CTO, COO
                $table->integer('active_assets_count')->default(0);
                $table->integer('available_count')->default(0);
                $table->integer('repair_count')->default(0);
                $table->integer('missing_count')->default(0);
                $table->decimal('purchase_value', 14, 2)->default(0.00);
                $table->integer('exceptions_count')->default(0);
                $table->timestamps();
            });
        }

        // 4. Employees
        if (!Schema::hasTable('fg_itam_employees')) {
            Schema::create('fg_itam_employees', function (Blueprint $table) {
                $table->id();
                $table->string('employee_id', 50)->nullable();
                $table->string('name', 150);
                $table->string('email', 150)->nullable();
                $table->string('department', 100);
                $table->string('location', 150)->default('KL Office');
                $table->integer('assigned_assets_count')->default(0);
                $table->integer('pending_returns_count')->default(0);
                $table->integer('exceptions_count')->default(0);
                $table->decimal('asset_value', 14, 2)->default(0.00);
                $table->string('status', 50)->default('Active');
                $table->timestamps();
            });
        }

        // 5. Suppliers
        if (!Schema::hasTable('fg_itam_suppliers')) {
            Schema::create('fg_itam_suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('category', 100); // Hardware, Networking, Printers, Cloud
                $table->string('contact_email', 150)->nullable();
                $table->string('contact_phone', 50)->nullable();
                $table->integer('open_pos_count')->default(0);
                $table->decimal('ytd_spend', 14, 2)->default(0.00);
                $table->string('status', 50)->default('Active');
                $table->timestamps();
            });
        }

        // 6. Purchases
        if (!Schema::hasTable('fg_itam_purchases')) {
            Schema::create('fg_itam_purchases', function (Blueprint $table) {
                $table->string('id', 50)->primary(); // PUR-2026-0148
                $table->string('supplier_name', 150);
                $table->string('invoice_number', 100);
                $table->date('purchase_date');
                $table->decimal('amount', 14, 2);
                $table->string('assets_description', 255);
                $table->string('status', 50)->default('Received'); // Received, Registered, Pending
                $table->timestamps();
            });
        }

        // 7. Purchase Orders
        if (!Schema::hasTable('fg_itam_purchase_orders')) {
            Schema::create('fg_itam_purchase_orders', function (Blueprint $table) {
                $table->string('id', 50)->primary(); // PO-2026-0088
                $table->string('supplier_name', 150);
                $table->string('requested_by', 150);
                $table->date('po_date');
                $table->decimal('amount', 14, 2);
                $table->date('delivery_date')->nullable();
                $table->string('status', 50)->default('Approved'); // Approved, Ordered, Partial, Received
                $table->timestamps();
            });
        }

        // 8. Repairs
        if (!Schema::hasTable('fg_itam_repairs')) {
            Schema::create('fg_itam_repairs', function (Blueprint $table) {
                $table->string('id', 50)->primary(); // RPR-0261
                $table->string('asset_id', 50);
                $table->string('issue', 255);
                $table->date('opened_date');
                $table->date('target_return_date')->nullable();
                $table->date('actual_return_date')->nullable();
                $table->string('vendor', 150);
                $table->decimal('cost', 12, 2)->default(0.00);
                $table->string('status', 50)->default('Repairing'); // Repairing, Overdue, Awaiting Part, Completed
                $table->timestamps();
            });
        }

        // 9. Software
        if (!Schema::hasTable('fg_itam_software')) {
            Schema::create('fg_itam_software', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('vendor', 150);
                $table->string('type', 50)->default('Subscription'); // Subscription, Perpetual
                $table->integer('managed_devices_count')->default(0);
                $table->string('owner_department', 100);
                $table->string('status', 50)->default('Active');
                $table->timestamps();
            });
        }

        // 10. Licenses
        if (!Schema::hasTable('fg_itam_licenses')) {
            Schema::create('fg_itam_licenses', function (Blueprint $table) {
                $table->id();
                $table->string('software_name', 150);
                $table->integer('purchased_seats')->default(0);
                $table->integer('assigned_seats')->default(0);
                $table->integer('available_seats')->default(0);
                $table->integer('utilization_percent')->default(0);
                $table->string('status', 50)->default('Healthy'); // Healthy, Review, Overallocated
                $table->timestamps();
            });
        }

        // 11. Renewals
        if (!Schema::hasTable('fg_itam_renewals')) {
            Schema::create('fg_itam_renewals', function (Blueprint $table) {
                $table->string('id', 50)->primary(); // REN-0091
                $table->string('service_name', 150);
                $table->string('owner', 100);
                $table->date('renewal_date');
                $table->decimal('annual_cost', 14, 2);
                $table->string('status', 50)->default('Planned'); // Approval Needed, In Review, Planned, Approved
                $table->timestamps();
            });
        }

        // 12. Audits
        if (!Schema::hasTable('fg_itam_audits')) {
            Schema::create('fg_itam_audits', function (Blueprint $table) {
                $table->string('id', 50)->primary(); // AUD-2026-006
                $table->string('name', 150);
                $table->string('scope', 255);
                $table->string('period', 50);
                $table->integer('total_in_scope')->default(0);
                $table->integer('verified_count')->default(0);
                $table->integer('exceptions_count')->default(0);
                $table->decimal('accuracy_rate', 5, 2)->nullable();
                $table->string('status', 50)->default('In Progress'); // In Progress, Completed, Planned
                $table->timestamps();
            });
        }

        // 13. Audit Exceptions
        if (!Schema::hasTable('fg_itam_audit_exceptions')) {
            Schema::create('fg_itam_audit_exceptions', function (Blueprint $table) {
                $table->id();
                $table->string('asset_id', 50);
                $table->string('exception_type', 100); // Not Found, Wrong Location, Damaged, Wrong User
                $table->string('expected_info', 255);
                $table->string('observed_info', 255);
                $table->string('audit_id', 50);
                $table->string('owner_department', 100)->default('Service Desk');
                $table->string('status', 50)->default('Open');
                $table->timestamps();
            });
        }

        // 14. Transactions (Assign, Return, Transfer, Loan)
        if (!Schema::hasTable('fg_itam_transactions')) {
            Schema::create('fg_itam_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('type', 50); // Assign, Return, Transfer, Loan
                $table->string('asset_id', 50);
                $table->string('from_user', 150)->nullable();
                $table->string('to_user', 150)->nullable();
                $table->string('from_department', 100)->nullable();
                $table->string('to_department', 100)->nullable();
                $table->string('from_location', 150)->nullable();
                $table->string('to_location', 150)->nullable();
                $table->date('transaction_date')->nullable();
                $table->date('due_date')->nullable();
                $table->date('return_date')->nullable();
                $table->string('condition_at_time', 50)->nullable();
                $table->string('accessories', 255)->nullable();
                $table->boolean('checklist_power_on')->default(true);
                $table->boolean('checklist_charger')->default(true);
                $table->boolean('checklist_data_wiped')->default(true);
                $table->text('remarks')->nullable();
                $table->string('performed_by', 150)->default('Admin');
                $table->timestamps();
            });
        }

        // 15. Audit Activity Logs
        if (!Schema::hasTable('fg_itam_audit_logs')) {
            Schema::create('fg_itam_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->dateTime('timestamp');
                $table->string('user', 150);
                $table->string('action', 100);
                $table->string('object_type', 100);
                $table->string('object_id', 100);
                $table->text('before_state')->nullable();
                $table->text('after_state')->nullable();
                $table->timestamps();
            });
        }

        // 16. System Settings
        if (!Schema::hasTable('fg_itam_settings')) {
            Schema::create('fg_itam_settings', function (Blueprint $table) {
                $table->string('key', 100)->primary();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // 17. Master Assets Table (fg_itam_assets)
        if (!Schema::hasTable('fg_itam_assets')) {
            Schema::create('fg_itam_assets', function (Blueprint $table) {
                // Primary Identifier matching exact specification: IT-DEVICE(3 ALPHABET)-YEAR-NUMBER 5 series
                $table->string('id', 50)->primary();
                $table->string('asset_tag', 50)->nullable();
                $table->string('name', 255);
                $table->string('category_id', 10); // LAP, DSK, MON, MOB, SRV, NET, PRN, ACC
                $table->string('category_name', 100);
                $table->string('brand', 100);
                $table->string('model', 150);
                $table->string('serial_number', 100)->unique();
                $table->string('status', 50)->default('Available'); // Available, Assigned, In Repair, Loan, Reserved, Lost, Stolen, Damaged, Retired, Disposed

                // Digital Identity
                $table->string('hostname', 100)->nullable();
                $table->string('mac_address', 50)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('imei', 50)->nullable();
                $table->string('platform_device_id', 100)->nullable();
                $table->string('network_identifier', 100)->nullable();

                // Ownership & Custody
                $table->string('company', 100)->default('FirstGrade Group');
                $table->string('department', 100)->nullable();
                $table->string('cost_center', 100)->nullable();
                $table->string('asset_owner', 150)->nullable();
                $table->string('assigned_user', 150)->nullable();
                $table->string('custodian', 150)->nullable();
                $table->date('assigned_date')->nullable();
                $table->date('expected_return_date')->nullable();

                // Location
                $table->string('country', 50)->default('Malaysia');
                $table->string('office', 100)->default('Kuala Lumpur Office');
                $table->string('building', 100)->nullable();
                $table->string('floor', 50)->nullable();
                $table->string('room', 100)->nullable();
                $table->string('assigned_location', 150)->default('IT Store');
                $table->string('last_known_location', 150)->nullable();

                // Procurement
                $table->string('supplier_name', 150)->nullable();
                $table->string('purchase_order_id', 50)->nullable();
                $table->string('invoice_number', 100)->nullable();
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_cost', 12, 2)->default(0.00);
                $table->string('currency', 10)->default('MYR');

                // Warranty & Lifecycle
                $table->date('warranty_start')->nullable();
                $table->date('warranty_end')->nullable();
                $table->string('warranty_provider', 150)->nullable();
                $table->string('support_contract', 150)->nullable();
                $table->string('replacement_due', 50)->nullable(); // e.g. Aug 2030

                // Retirement & Disposal
                $table->date('retired_date')->nullable();
                $table->string('retirement_reason', 255)->nullable();
                $table->string('disposition', 100)->nullable(); // Recycle, Destroy, Donate, Sale
                $table->string('data_sanitization', 50)->nullable(); // Verified, Pending, N/A

                // Evidence & Last Seen
                $table->dateTime('last_electronically_seen')->nullable();
                $table->string('last_seen_source', 100)->nullable();
                $table->date('last_physical_verification')->nullable();
                $table->string('last_verified_by', 150)->nullable();
                $table->string('verification_status', 50)->default('Verified'); // Verified, Review Required, Missing

                // Physical Condition & Notes
                $table->string('condition', 50)->default('Good'); // New, Good, Fair, Damaged
                $table->string('accessories', 255)->nullable();
                $table->text('remarks')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fg_itam_assets');
        Schema::dropIfExists('fg_itam_settings');
        Schema::dropIfExists('fg_itam_audit_logs');
        Schema::dropIfExists('fg_itam_transactions');
        Schema::dropIfExists('fg_itam_audit_exceptions');
        Schema::dropIfExists('fg_itam_audits');
        Schema::dropIfExists('fg_itam_renewals');
        Schema::dropIfExists('fg_itam_licenses');
        Schema::dropIfExists('fg_itam_software');
        Schema::dropIfExists('fg_itam_repairs');
        Schema::dropIfExists('fg_itam_purchase_orders');
        Schema::dropIfExists('fg_itam_purchases');
        Schema::dropIfExists('fg_itam_suppliers');
        Schema::dropIfExists('fg_itam_employees');
        Schema::dropIfExists('fg_itam_departments');
        Schema::dropIfExists('fg_itam_locations');
        Schema::dropIfExists('fg_itam_categories');
    }
};
