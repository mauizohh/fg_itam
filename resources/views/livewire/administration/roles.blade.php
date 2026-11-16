<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Roles &amp; Permissions</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Six-role RBAC model combining System &amp; IT Administration, mapped dynamically from Admidio and ITAM overrides.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn-secondary text-xs">
            Manage User Role Assignments
        </a>
    </div>

    <!-- Role Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($roles as $role)
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-heading font-bold text-[#3b4048]">{{ $role->label() }}</h2>
                    <span class="{{ $role->badgeClass() }}">{{ $role->value }}</span>
                </div>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $role->description() }}</p>
            </div>
        @endforeach
    </div>

    <!-- Table: Role Permission Matrix -->
    <x-table-section title="Role Permission Matrix (Section 9 Alignment)">
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Capability Area</th>
                        <th>System &amp; IT Admin</th>
                        <th>IT Support</th>
                        <th>Department Manager</th>
                        <th>Finance</th>
                        <th>Employee</th>
                        <th>Auditor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $p)
                        @php
                            $badgeFor = function (string $val): string {
                                return match (true) {
                                    $val === 'Yes' => 'pill low',
                                    $val === 'No' => 'pill closed',
                                    str_contains($val, 'Read') || str_contains($val, 'Scope') || str_contains($val, 'Own') => 'pill normal',
                                    default => 'pill high',
                                };
                            };
                        @endphp
                        <tr>
                            <td class="font-bold text-gray-900">{{ $p['perm'] }}</td>
                            <td><span class="{{ $badgeFor($p['system_it_admin']) }}">{{ $p['system_it_admin'] }}</span></td>
                            <td><span class="{{ $badgeFor($p['it_support']) }}">{{ $p['it_support'] }}</span></td>
                            <td><span class="{{ $badgeFor($p['department_manager']) }}">{{ $p['department_manager'] }}</span></td>
                            <td><span class="{{ $badgeFor($p['finance']) }}">{{ $p['finance'] }}</span></td>
                            <td><span class="{{ $badgeFor($p['employee']) }}">{{ $p['employee'] }}</span></td>
                            <td><span class="{{ $badgeFor($p['auditor']) }}">{{ $p['auditor'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-table-section>
</div>
