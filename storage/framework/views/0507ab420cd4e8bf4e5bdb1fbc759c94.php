<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-extrabold text-[#3b4048]">Dashboard</h1>
            <p class="text-xs text-[#69717d] mt-0.5">Executive overview of inventory, accountability, risk and upcoming actions.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('assets.index')); ?>" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Asset</span>
            </a>
            <a href="<?php echo e(route('audits.new')); ?>" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span>Audit Scan</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- TOTAL ASSETS -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition">
            <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider font-heading">TOTAL ASSETS</span>
            <strong class="block text-3xl font-heading font-extrabold text-[#3b4048] mt-2">1,250</strong>
            <small class="block text-xs text-gray-400 mt-1">Across all active categories</small>
        </div>

        <!-- ASSIGNED -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition">
            <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider font-heading">ASSIGNED</span>
            <strong class="block text-3xl font-heading font-extrabold text-[#3b4048] mt-2">920</strong>
            <small class="block text-xs text-emerald-600 font-bold mt-1">73.6% of total</small>
        </div>

        <!-- AVAILABLE -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition">
            <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider font-heading">AVAILABLE</span>
            <strong class="block text-3xl font-heading font-extrabold text-[#3b4048] mt-2">130</strong>
            <small class="block text-xs text-gray-400 mt-1">Ready for allocation</small>
        </div>

        <!-- ATTENTION NEEDED -->
        <div class="bg-white rounded-xl p-5 border border-red-200 shadow-sm hover:shadow-md transition bg-gradient-to-br from-red-50/50 to-white">
            <span class="block text-xs font-bold text-red-600 uppercase tracking-wider font-heading">ATTENTION NEEDED</span>
            <strong class="block text-3xl font-heading font-extrabold text-[#b91c1c] mt-2">33</strong>
            <small class="block text-xs text-red-500 mt-1">Repair, missing or overdue</small>
        </div>
    </div>

    <!-- Middle Split: Asset Status & Upcoming Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Asset Status Breakdown -->
        <div class="lg:col-span-2 bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-bold text-base text-[#3b4048]">Asset Status</h3>
                <span class="text-xs text-gray-400">Real-time health breakdown</span>
            </div>

            <!-- Multi-color Progress Bar -->
            <div class="h-4 w-full bg-gray-100 rounded-full overflow-hidden flex shadow-inner">
                <div style="width: 73.6%" class="bg-[#388bfd] h-full" title="Assigned: 920 (73.6%)"></div>
                <div style="width: 10.4%" class="bg-[#2ea043] h-full" title="Available: 130 (10.4%)"></div>
                <div style="width: 6.8%" class="bg-[#d29922] h-full" title="Repair: 85 (6.8%)"></div>
                <div style="width: 9.2%" class="bg-[#64748b] h-full" title="Other: 115 (9.2%)"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-100 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#388bfd]"></span>
                    <div>
                        <span class="text-gray-500 block">Assigned</span>
                        <strong class="text-gray-900 font-heading text-sm">920</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#2ea043]"></span>
                    <div>
                        <span class="text-gray-500 block">Available</span>
                        <strong class="text-gray-900 font-heading text-sm">130</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#d29922]"></span>
                    <div>
                        <span class="text-gray-500 block">In Repair</span>
                        <strong class="text-gray-900 font-heading text-sm">85</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#64748b]"></span>
                    <div>
                        <span class="text-gray-500 block">Other/Retired</span>
                        <strong class="text-gray-900 font-heading text-sm">115</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Actions -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <h3 class="font-heading font-bold text-base text-[#3b4048] mb-4">Upcoming Actions</h3>
            <div class="space-y-3.5">
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-red-100 text-[#a92d31] font-heading font-extrabold flex items-center justify-center text-xs">12</span>
                        <span class="text-xs text-gray-700 font-medium">Warranties expire in 30 days</span>
                    </div>
                    <a href="<?php echo e(route('maintenance.warranty')); ?>" class="text-[11px] font-bold text-[#a92d31] hover:underline">View</a>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 font-heading font-extrabold flex items-center justify-center text-xs">7</span>
                        <span class="text-xs text-gray-700 font-medium">Asset returns overdue</span>
                    </div>
                    <a href="<?php echo e(route('transactions.loan')); ?>" class="text-[11px] font-bold text-[#a92d31] hover:underline">View</a>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 font-heading font-extrabold flex items-center justify-center text-xs">3</span>
                        <span class="text-xs text-gray-700 font-medium">Software renewals this month</span>
                    </div>
                    <a href="<?php echo e(route('software.renewals')); ?>" class="text-[11px] font-bold text-[#a92d31] hover:underline">View</a>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 font-heading font-extrabold flex items-center justify-center text-xs">11</span>
                        <span class="text-xs text-gray-700 font-medium">Assets due for replacement</span>
                    </div>
                    <a href="<?php echo e(route('assets.index')); ?>" class="text-[11px] font-bold text-[#a92d31] hover:underline">View</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-heading font-bold text-base text-[#3b4048]">Recent Activity</h3>
            <a href="<?php echo e(route('admin.audit-logs')); ?>" class="text-xs font-bold text-[#a92d31] hover:underline">View All Logs</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left itam-table">
                <thead>
                    <tr>
                        <th class="w-28">Time</th>
                        <th>Asset</th>
                        <th>Action</th>
                        <th class="w-32">User</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-bold text-gray-700">10:42</td>
                        <td>
                            <a href="<?php echo e(route('assets.detail', 'IT-LAP-2026-00123')); ?>" class="text-[#a92d31] hover:underline font-bold font-heading">
                                IT-LAP-2026-00123
                            </a>
                        </td>
                        <td>Assigned to Sarah Lim</td>
                        <td><span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-semibold">Admin</span></td>
                    </tr>
                    <tr>
                        <td class="font-bold text-gray-700">09:18</td>
                        <td>
                            <a href="<?php echo e(route('assets.detail', 'IT-MON-2026-00391')); ?>" class="text-[#a92d31] hover:underline font-bold font-heading">
                                IT-MON-2026-00391
                            </a>
                        </td>
                        <td>Returned to IT Store</td>
                        <td><span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-semibold">Support</span></td>
                    </tr>
                    <tr>
                        <td class="font-bold text-gray-700">Yesterday</td>
                        <td>
                            <a href="<?php echo e(route('assets.detail', 'IT-NET-2026-00201')); ?>" class="text-[#a92d31] hover:underline font-bold font-heading">
                                IT-NET-2026-00201
                            </a>
                        </td>
                        <td>Warranty updated</td>
                        <td><span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-semibold">Admin</span></td>
                    </tr>
                    <tr>
                        <td class="font-bold text-gray-700">Yesterday</td>
                        <td>
                            <a href="<?php echo e(route('assets.detail', 'IT-LAP-2026-00418')); ?>" class="text-[#a92d31] hover:underline font-bold font-heading">
                                IT-LAP-2026-00418
                            </a>
                        </td>
                        <td>Marked in repair</td>
                        <td><span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-semibold">Support</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Mauizoh\fg-itam\resources\views/livewire/dashboard.blade.php ENDPATH**/ ?>