<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Locations</h1>
        <p class="text-xs text-[#69717d] mt-0.5">Maintain offices, buildings, rooms and storage locations used by the asset register.</p>
    </div>

    <!-- 2 Column Master/Detail layout matching UI 26 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Tree View -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-heading font-bold text-sm text-[#3b4048] pb-2 border-b">Locations Hierarchy</h3>
            <div class="space-y-2 text-xs">
                <div class="font-bold text-gray-800 flex items-center gap-1.5">
                    <span class="text-gray-400">&blacktriangledown;</span>
                    <span>Malaysia</span>
                </div>
                <div class="pl-4 space-y-1.5 border-l-2 border-gray-100 ml-1">
                    <div>
                        <button wire:click="selectOffice('Kuala Lumpur Office')"
                                class="flex items-center gap-1.5 font-bold {{ $selectedOffice === 'Kuala Lumpur Office' ? 'text-[#a92d31]' : 'text-gray-700' }} hover:text-[#a92d31] transition">
                            <span>&blacktriangledown;</span>
                            <span>Kuala Lumpur Office</span>
                        </button>
                        <div class="pl-4 py-1 space-y-1 text-gray-500">
                            <div class="hover:text-gray-900 cursor-pointer">Level 5 - Finance</div>
                            <div class="hover:text-gray-900 cursor-pointer">Level 6 - Engineering</div>
                            <div class="hover:text-gray-900 cursor-pointer">IT Store</div>
                            <div class="hover:text-gray-900 cursor-pointer">Server Room</div>
                        </div>
                    </div>
                    <div>
                        <button wire:click="selectOffice('Penang Office')"
                                class="flex items-center gap-1.5 font-bold {{ $selectedOffice === 'Penang Office' ? 'text-[#a92d31]' : 'text-gray-700' }} hover:text-[#a92d31] transition">
                            <span>+</span>
                            <span>Penang Office</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Office Overview & Sub-areas matching UI 26 -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-6">
                <div>
                    <h2 class="text-xl font-heading font-extrabold text-gray-900">{{ $selectedOffice }}</h2>
                    <span class="text-xs text-gray-400">Headquarters / Regional Hub</span>
                </div>

                <!-- 3 Top Metrics -->
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider font-heading">ASSETS</span>
                        <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ $assetsTotal }}</strong>
                        <small class="block text-xs text-gray-400 mt-0.5">Current</small>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider font-heading">EMPLOYEES</span>
                        <strong class="block text-2xl font-heading font-extrabold text-[#3b4048] mt-1">{{ $employeesTotal }}</strong>
                        <small class="block text-xs text-gray-400 mt-0.5">Active</small>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider font-heading">EXCEPTIONS</span>
                        <strong class="block text-2xl font-heading font-extrabold text-[#b91c1c] mt-1">{{ $exceptionsTotal }}</strong>
                        <small class="block text-xs text-red-500 font-semibold mt-0.5">Open</small>
                    </div>
                </div>

                <!-- Areas Table -->
                <div class="overflow-x-auto pt-2">
                    <table class="w-full text-left itam-table">
                        <thead>
                            <tr>
                                <th>Area</th>
                                <th>Assets</th>
                                <th>Owner</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locations as $loc)
                            <tr>
                                <td class="font-bold text-gray-800">{{ $loc->area }}</td>
                                <td class="font-bold font-heading text-gray-800">{{ $loc->assets_count }}</td>
                                <td class="font-semibold text-gray-700">{{ $loc->owner_department }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray-400">No designated zones configured.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
