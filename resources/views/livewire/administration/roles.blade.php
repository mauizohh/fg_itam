<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Roles</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Define permission groups and enforce least-privilege access.</p>
        </div>
        <button class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Role</span>
        </button>
    </div>

    <!-- Table: Role Permission Matrix -->
    <x-table-section title="Role Permission Matrix">
        <table class="w-full text-left itam-table">
            <thead>
                <tr>
                    <th>Permission</th>
                    <th>System Admin</th>
                    <th>IT Admin</th>
                    <th>IT Support</th>
                    <th>Finance</th>
                    <th>Auditor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $p)
                <tr>
                    <td class="font-bold text-gray-900">{{ $p['perm'] }}</td>
                    <td><span class="pill low">{{ $p['admin'] }}</span></td>
                    <td><span class="pill {{ $p['it_admin'] === 'Yes' ? 'low' : 'closed' }}">{{ $p['it_admin'] }}</span></td>
                    <td><span class="pill {{ $p['it_support'] === 'Yes' ? 'low' : ($p['it_support'] === 'Limited' ? 'high' : 'closed') }}">{{ $p['it_support'] }}</span></td>
                    <td><span class="pill {{ $p['finance'] === 'Yes' ? 'low' : ($p['finance'] === 'Read' ? 'normal' : 'closed') }}">{{ $p['finance'] }}</span></td>
                    <td><span class="pill {{ $p['auditor'] === 'Yes' ? 'low' : ($p['auditor'] === 'Read' ? 'normal' : 'closed') }}">{{ $p['auditor'] }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-section>
</div>
