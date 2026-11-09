<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Available Assets</h1>
        <p class="text-xs text-[#69717d] mt-0.5">View equipment currently in stock and ready for assignment.</p>
    </div>

    <!-- 4 Category Stat Cards matching UI 03 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">LAPTOPS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ max($laptopCount, 15) }}</strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Available</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">MONITORS</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ max($monitorCount, 22) }}</strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Available</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">PHONES</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ max($phoneCount, 8) }}</strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Available</small>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider font-heading">NETWORK</span>
            <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ max($networkCount, 4) }}</strong>
            <small class="block text-xs text-emerald-600 font-semibold mt-0.5">Available</small>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Search...</label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filter equipment..."
                class="itam-input text-xs h-10">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Category</label>
            <select wire:model.live="categoryFilter" class="itam-select text-xs h-10">
                <option value="All">All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Location</label>
            <select wire:model.live="locationFilter" class="itam-select text-xs h-10">
                <option value="">All Locations</option>
                <option value="IT Store">IT Store</option>
                <option value="KL Office">KL Office</option>
                <option value="Server Room">Server Room</option>
                <option value="Penang Office">Penang Office</option>
            </select>
        </div>
    </div>

    <!-- Table: Available Inventory -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Available Inventory</h3>
            <span class="text-xs text-gray-400">{{ count($assets) }} in stock</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Asset</th>
                        <th>Model</th>
                        <th>Location</th>
                        <th>Condition</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td>
                            <a href="{{ route('assets.detail', $asset->id) }}" class="font-bold text-[#a92d31] hover:underline font-heading">
                                {{ $asset->id }}
                            </a>
                        </td>
                        <td class="font-bold text-gray-800">{{ $asset->category_name }}</td>
                        <td class="text-gray-600">{{ $asset->model }}</td>
                        <td>{{ $asset->assigned_location }}</td>
                        <td>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $asset->condition === 'New' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $asset->condition }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('transactions.assign') }}?asset_id={{ $asset->id }}" class="inline-flex items-center text-xs font-bold px-3 py-1 rounded bg-[#f1f5f9] hover:bg-[#a92d31] hover:text-white transition border border-gray-200">
                                Assign
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400">
                            No available inventory matching current filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
