<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Users</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Manage system users, authentication status and role assignment.</p>
        </div>
    </div>

    <!-- Filters Bar matching UI 27 -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search user..." class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Role</label>
            <select wire:model.live="roleFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                <option value="System Administrator">System Administrator</option>
                <option value="IT Support">IT Support</option>
                <option value="Finance">Finance</option>
                <option value="Auditor">Auditor</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Table: System Users matching UI 27 -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">System Users</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Last Login</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td class="font-bold text-gray-900">{{ $u['name'] }}</td>
                        <td class="text-xs font-mono text-gray-600">{{ $u['email'] }}</td>
                        <td>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                {{ $u['role'] }}
                            </span>
                        </td>
                        <td class="text-xs text-gray-600">{{ $u['last_login'] }}</td>
                        <td><span class="pill low">{{ $u['status'] }}</span></td>
                        <td class="text-center">
                            <button class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded bg-gray-50 hover:bg-[#a92d31] hover:text-white border border-gray-200 transition">
                                Edit
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
