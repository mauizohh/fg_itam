<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Users</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Live Admidio directory synchronized with FG ITAM role overrides (`fg_itam_user_roles`).</p>
        </div>
        <a href="{{ route('admin.roles') }}" class="btn-secondary text-xs">View Role Matrix</a>
    </div>

    @if (session()->has('success'))
        <div role="status" aria-live="polite" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label for="user-search" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search User / Login / Dept</label>
            <input id="user-search" type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, login, or email..." class="itam-input text-xs h-10">
        </div>
        <div>
            <label for="role-filter" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">ITAM Role</label>
            <select id="role-filter" wire:model.live="roleFilter" class="itam-select text-xs h-10">
                <option value="All">All Roles</option>
                @foreach ($roleOptions as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status-filter" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</label>
            <select id="status-filter" wire:model.live="statusFilter" class="itam-select text-xs h-10">
                <option value="All">All Statuses</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Table: System Users -->
    <x-table-section title="Admidio Employee Directory" description="Effective ITAM role resolved from override table or Admidio department membership">
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Admidio Dept(s)</th>
                        <th>Effective Role</th>
                        <th>Source</th>
                        <th>Last Login</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td>
                                <div class="font-bold text-gray-900">{{ $u['name'] }}</div>
                                <div class="text-[11px] font-mono text-gray-500">{{ $u['username'] ?: 'no-login' }} &bull; {{ $u['email'] }}</div>
                            </td>
                            <td class="text-xs text-gray-600">{{ $u['departments'] }}</td>
                            <td>
                                <span class="{{ $u['role']->badgeClass() }}">
                                    {{ $u['role']->label() }}
                                </span>
                            </td>
                            <td class="text-xs">
                                @if ($u['role_source'] === 'override')
                                    <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-semibold">Override</span>
                                @else
                                    <span class="text-gray-500">Admidio</span>
                                @endif
                            </td>
                            <td class="text-xs text-gray-600 font-mono">{{ $u['last_login'] }}</td>
                            <td>
                                <span class="pill {{ $u['is_active'] ? 'low' : 'closed' }}">
                                    {{ $u['is_active'] ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" wire:click="openEdit({{ $u['usr_id'] }})" class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded bg-gray-50 hover:bg-[#a92d31] hover:text-white border border-gray-200 transition">
                                    Edit Role
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-sm text-gray-400">No matching Admidio users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </x-table-section>

    <!-- Edit Role Override Modal -->
    @if ($editUsrId !== null)
        <div class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="edit-role-title">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md p-6 space-y-4">
                <div>
                    <h2 id="edit-role-title" class="text-lg font-heading font-bold text-[#3b4048]">Edit ITAM Role Assignment</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $editUserName }} (<span class="font-mono text-[#a92d31]">{{ $editLoginName }}</span>)
                    </p>
                </div>

                <form wire:submit="saveUserRole" class="space-y-4">
                    <div>
                        <label for="edit-role-select" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">ITAM Role</label>
                        <select id="edit-role-select" wire:model="editRole" class="itam-select w-full text-sm">
                            <option value="auto">Auto (Derive from Admidio Department &amp; Leadership)</option>
                            @foreach ($roleOptions as $option)
                                <option value="{{ $option->value }}">{{ $option->label() }}</option>
                            @endforeach
                        </select>
                        @error('editRole') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="edit-is-active" type="checkbox" wire:model="editIsActive" class="rounded border-gray-300 text-[#a92d31] focus:ring-[#a92d31]">
                        <label for="edit-is-active" class="text-sm text-[#3b4048] font-medium">ITAM Access Active</label>
                    </div>

                    <div>
                        <label for="edit-notes" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Notes</label>
                        <input id="edit-notes" type="text" wire:model="editNotes" placeholder="Reason for override or suspension..." class="itam-input w-full text-sm">
                        @error('editNotes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" wire:click="cancelEdit" class="btn-secondary text-xs">Cancel</button>
                        <button type="submit" class="btn-primary text-xs">Save Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
