<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($title) ? $title . ' - FG IT Asset Management' : 'FG IT Asset Management' }}</title>
    <link rel="icon" href="{{ asset('images/fg_logo.png') }}" type="image/png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- WireUI & Vite Scripts -->
    @wireUiScripts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f9fafb] text-[#3b4048] antialiased font-sans selection:bg-[#a92d31] selection:text-white"
      x-data="{
          sidebarOpen: window.innerWidth >= 1024,
          mobileNav: false,
          quickSearchOpen: false,
          isMobile: window.innerWidth < 768,
          init() {
              this.isMobile = window.innerWidth < 768;
          },
          toggleSidebar() {
              if (this.isMobile) {
                  this.mobileNav = !this.mobileNav;
              } else {
                  this.sidebarOpen = !this.sidebarOpen;
              }
          },
          closeMobile() {
              this.mobileNav = false;
          },
          handleResize() {
              const wasMobile = this.isMobile;
              this.isMobile = window.innerWidth < 768;
              if (!this.isMobile && wasMobile) {
                  this.mobileNav = false;
              }
          }
      }"
      @resize.window.debounce.100ms="handleResize()"
      @keydown.escape.window="closeMobile()">
    <x-dialog />
    <x-notifications />

    <!-- TOPBAR / HEADER (Fixed 70px) -->
    <header class="fixed top-0 left-0 right-0 z-50 h-[70px] bg-white/95 backdrop-blur border-b border-gray-200 shadow-sm flex items-center px-4 md:px-6 justify-between">
        <div class="flex items-center gap-3">
            <button @click="toggleSidebar()" class="text-gray-500 hover:text-[#a92d31] p-1.5 rounded-lg focus:outline-none transition" aria-label="Toggle Navigation">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 sm:gap-3 hover:opacity-90 transition">
                <img src="{{ asset('images/fg_logo.png') }}" alt="Logo" class="h-9 sm:h-10">
                <h1 class="text-base sm:text-lg font-heading font-bold text-fg-red hidden sm:block truncate">FG IT ASSET MANAGEMENT</h1>
            </a>
        </div>

        <!-- Right User Actions -->
        @php
            $currentUser = auth()->user();
        @endphp
        <div class="flex items-center gap-3">
            <a href="http://new-fg-ithd.test" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-heading font-bold text-[#a92d31] bg-red-50 hover:bg-red-100 rounded-lg transition border border-red-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span>Helpdesk Bridge</span>
            </a>

            @if ($currentUser)
                <div class="flex items-center gap-2 pl-2">
                    <div class="w-8 h-8 rounded-full bg-[#8f2428] text-white flex items-center justify-center font-heading font-bold text-xs shadow-sm" title="{{ $currentUser->name }} ({{ $currentUser->role_label }})">
                        {{ $currentUser->initials }}
                    </div>
                    <div class="hidden xl:flex flex-col text-left">
                        <span class="text-xs font-bold text-[#3b4048] leading-tight">{{ $currentUser->name }}</span>
                        <span class="text-[10px] text-gray-500">{{ $currentUser->role_label }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="ml-1">
                        @csrf
                        <button type="submit" class="px-2.5 py-1.5 text-xs font-semibold text-gray-600 hover:text-[#a92d31] hover:bg-red-50 border border-gray-200 rounded-lg transition focus-visible:outline-2 focus-visible:outline-[#a92d31]">
                            Sign Out
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </header>

    <!-- MAIN WRAPPER -->
    <div class="flex pt-[70px] min-h-screen w-full">
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div x-show="mobileNav"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeMobile()"
             class="fixed inset-0 top-[70px] bg-black/50 backdrop-blur-sm z-40 md:hidden"
             style="display: none;"></div>

        <!-- SIDEBAR -->
        <aside
            :class="{
                'w-[260px]': isMobile,
                'w-[238px]': !isMobile && sidebarOpen,
                'w-[68px]': !isMobile && !sidebarOpen,
                'translate-x-0 shadow-2xl': mobileNav,
                '-translate-x-full': !mobileNav
            }"
            class="fixed md:sticky top-[70px] bottom-0 left-0 bg-[#8f2428] text-white overflow-y-auto overflow-x-hidden transition-all duration-200 z-50 md:z-30 select-none shadow-xl flex flex-col justify-between shrink-0 md:translate-x-0 max-w-[85vw] md:max-w-none h-[calc(100vh-70px)]">

            <!-- Mobile Drawer Header -->
            <div class="flex items-center justify-between px-3 py-2.5 border-b border-white/10 md:hidden">
                <span class="text-xs font-bold tracking-wider uppercase text-white/80">Navigation</span>
                <button @click="closeMobile()" class="p-1 rounded text-white/70 hover:text-white hover:bg-white/10" aria-label="Close Navigation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="py-3 px-2 space-y-1" @click="if (isMobile && $event.target.closest('a')) closeMobile()">
                @if ($currentUser && $currentUser->hasPermission('dashboard.view'))
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('dashboard*') ? 'bg-white/20 text-white shadow-inner' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span x-show="isMobile || sidebarOpen" class="truncate">Dashboard</span>
                    </a>
                @endif

                @if ($currentUser && $currentUser->hasPermission('my_assets.view'))
                    <!-- My Equipment -->
                    <a href="{{ route('my-assets') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('my-assets') ? 'bg-white/20 text-white shadow-inner' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-show="isMobile || sidebarOpen" class="truncate">My Equipment</span>
                    </a>
                @endif

                @if ($currentUser && ($currentUser->hasPermission('assets.view') || $currentUser->hasPermission('assets.retired.view')))
                    <!-- Assets (Dropdown/Group) -->
                    <div x-data="{ open: {{ request()->routeIs('assets*') ? 'true' : 'false' }} }">
                        <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('assets*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span x-show="isMobile || sidebarOpen" class="truncate">Assets</span>
                            </div>
                            <svg x-show="isMobile || sidebarOpen" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open && (isMobile || sidebarOpen)" class="pl-8 pr-1 py-1 space-y-1">
                            @if ($currentUser->hasPermission('assets.view'))
                                <a href="{{ route('assets.index') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('assets.index') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">All Assets</a>
                                <a href="{{ route('assets.available') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('assets.available') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Available Assets</a>
                                <a href="{{ route('assets.assigned') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('assets.assigned') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Assigned Assets</a>
                                <a href="{{ route('assets.repair') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('assets.repair') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Assets in Repair</a>
                            @endif
                            @if ($currentUser->hasPermission('assets.retired.view'))
                                <a href="{{ route('assets.retired') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('assets.retired') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Retired Assets</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($currentUser && $currentUser->hasPermission('people.view'))
                    <!-- People -->
                    <div x-data="{ open: {{ request()->routeIs('people*') ? 'true' : 'false' }} }">
                        <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('people*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span x-show="isMobile || sidebarOpen" class="truncate">People</span>
                            </div>
                            <svg x-show="isMobile || sidebarOpen" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open && (isMobile || sidebarOpen)" class="pl-8 pr-1 py-1 space-y-1">
                            <a href="{{ route('people.employees') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('people.employees') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Employees</a>
                            <a href="{{ route('people.departments') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('people.departments') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Departments</a>
                        </div>
                    </div>
                @endif

                @if ($currentUser && ($currentUser->hasPermission('transactions.manage') || $currentUser->hasPermission('transactions.history')))
                    <!-- Transactions -->
                    <div x-data="{ open: {{ request()->routeIs('transactions*') ? 'true' : 'false' }} }">
                        <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('transactions*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                <span x-show="isMobile || sidebarOpen" class="truncate">Transactions</span>
                            </div>
                            <svg x-show="isMobile || sidebarOpen" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open && (isMobile || sidebarOpen)" class="pl-8 pr-1 py-1 space-y-1">
                            @if ($currentUser->hasPermission('transactions.manage'))
                                <a href="{{ route('transactions.assign') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('transactions.assign') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Assign Asset</a>
                                <a href="{{ route('transactions.return') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('transactions.return') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Return Asset</a>
                                <a href="{{ route('transactions.transfer') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('transactions.transfer') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Transfer Asset</a>
                                <a href="{{ route('transactions.loan') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('transactions.loan') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Loan Asset</a>
                            @endif
                            @if ($currentUser->hasPermission('transactions.history'))
                                <a href="{{ route('transactions.history') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('transactions.history') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Transaction History</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($currentUser && $currentUser->hasPermission('procurement.view'))
                    <!-- Procurement -->
                    <div x-data="{ open: {{ request()->routeIs('procurement*') ? 'true' : 'false' }} }">
                        <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('procurement*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span x-show="isMobile || sidebarOpen" class="truncate">Procurement</span>
                            </div>
                            <svg x-show="isMobile || sidebarOpen" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open && (isMobile || sidebarOpen)" class="pl-8 pr-1 py-1 space-y-1">
                            <a href="{{ route('procurement.suppliers') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('procurement.suppliers') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Suppliers</a>
                            <a href="{{ route('procurement.purchases') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('procurement.purchases') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Purchases</a>
                            <a href="{{ route('procurement.purchase-orders') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('procurement.purchase-orders') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Purchase Orders</a>
                        </div>
                    </div>
                @endif

                @if ($currentUser && $currentUser->hasPermission('maintenance.view'))
                    <!-- Maintenance -->
                    <div x-data="{ open: {{ request()->routeIs('maintenance*') ? 'true' : 'false' }} }">
                        <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('maintenance*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-show="isMobile || sidebarOpen" class="truncate">Maintenance</span>
                            </div>
                            <svg x-show="isMobile || sidebarOpen" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open && (isMobile || sidebarOpen)" class="pl-8 pr-1 py-1 space-y-1">
                            <a href="{{ route('maintenance.repairs') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('maintenance.repairs') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Repairs</a>
                            <a href="{{ route('maintenance.warranty') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('maintenance.warranty') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Warranty</a>
                        </div>
                    </div>
                @endif

                @if ($currentUser && $currentUser->hasPermission('software.view'))
                    <!-- Software -->
                    <div x-data="{ open: {{ request()->routeIs('software*') ? 'true' : 'false' }} }">
                        <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('software*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                </svg>
                                <span x-show="isMobile || sidebarOpen" class="truncate">Software</span>
                            </div>
                            <svg x-show="isMobile || sidebarOpen" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open && (isMobile || sidebarOpen)" class="pl-8 pr-1 py-1 space-y-1">
                            <a href="{{ route('software.index') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('software.index') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Software Catalog</a>
                            <a href="{{ route('software.licenses') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('software.licenses') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Licenses</a>
                            <a href="{{ route('software.renewals') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('software.renewals') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Renewals</a>
                        </div>
                    </div>
                @endif

                @if ($currentUser && ($currentUser->hasPermission('audits.scan') || $currentUser->hasPermission('audits.history.view') || $currentUser->hasPermission('audits.exceptions.view')))
                    <!-- Audits -->
                    <div x-data="{ open: {{ request()->routeIs('audits*') ? 'true' : 'false' }} }">
                        <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('audits*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                <span x-show="isMobile || sidebarOpen" class="truncate">Audits</span>
                            </div>
                            <svg x-show="isMobile || sidebarOpen" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open && (isMobile || sidebarOpen)" class="pl-8 pr-1 py-1 space-y-1">
                            @if ($currentUser->hasPermission('audits.scan'))
                                <a href="{{ route('audits.new') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('audits.new') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">New Audit (Scanner)</a>
                            @endif
                            @if ($currentUser->hasPermission('audits.history.view'))
                                <a href="{{ route('audits.history') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('audits.history') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Audit History</a>
                            @endif
                            @if ($currentUser->hasPermission('audits.exceptions.view'))
                                <a href="{{ route('audits.exceptions') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('audits.exceptions') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Exceptions</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($currentUser && $currentUser->hasPermission('reports.view'))
                    <!-- Reports -->
                    <a href="{{ route('reports.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('reports*') ? 'bg-white/20 text-white shadow-inner' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span x-show="isMobile || sidebarOpen" class="truncate">Reports</span>
                    </a>
                @endif

                @if ($currentUser && ($currentUser->hasPermission('admin.categories') || $currentUser->hasPermission('admin.locations') || $currentUser->hasPermission('admin.users') || $currentUser->hasPermission('admin.roles') || $currentUser->hasPermission('admin.settings') || $currentUser->hasPermission('admin.audit_logs')))
                    <!-- Administration -->
                    <div x-data="{ open: {{ request()->routeIs('admin*') ? 'true' : 'false' }} }">
                        <button @click="if (!isMobile && !sidebarOpen) { sidebarOpen = true; open = true; } else { open = !open; }"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-heading font-bold transition-colors {{ request()->routeIs('admin*') ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                                <span x-show="isMobile || sidebarOpen" class="truncate">Administration</span>
                            </div>
                            <svg x-show="isMobile || sidebarOpen" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open && (isMobile || sidebarOpen)" class="pl-8 pr-1 py-1 space-y-1">
                            @if ($currentUser->hasPermission('admin.categories'))
                                <a href="{{ route('admin.categories') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('admin.categories') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Asset Categories</a>
                            @endif
                            @if ($currentUser->hasPermission('admin.locations'))
                                <a href="{{ route('admin.locations') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('admin.locations') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Locations</a>
                            @endif
                            @if ($currentUser->hasPermission('admin.users'))
                                <a href="{{ route('admin.users') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('admin.users') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Users</a>
                            @endif
                            @if ($currentUser->hasPermission('admin.roles'))
                                <a href="{{ route('admin.roles') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('admin.roles') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Roles</a>
                            @endif
                            @if ($currentUser->hasPermission('admin.settings'))
                                <a href="{{ route('admin.settings') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('admin.settings') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Settings</a>
                            @endif
                            @if ($currentUser->hasPermission('admin.audit_logs'))
                                <a href="{{ route('admin.audit-logs') }}" class="block px-2.5 py-1.5 rounded text-[11px] {{ request()->routeIs('admin.audit-logs') ? 'text-white font-bold bg-white/15' : 'text-white/70 hover:text-white hover:bg-white/5' }}">Audit Logs</a>
                            @endif
                        </div>
                    </div>
                @endif
            </nav>

            <!-- Bottom System Status -->
            <div x-show="isMobile || sidebarOpen" class="p-3 bg-black/20 m-2 rounded-lg text-[10px] text-white/80">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="font-bold">Sync Active</span>
                </div>
                <p class="mt-1 text-white/60">Helpdesk Asset Bridge linked</p>
            </div>
        </aside>

        <!-- MAIN PAGE CONTENT -->
        <main class="flex-1 min-w-0 transition-all duration-200 p-4 md:p-8 min-h-[calc(100vh-70px)]">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Livewire Quick Helpdesk Bridge Widget -->
    @if ($currentUser && $currentUser->hasPermission('assets.lookup'))
        @livewire('components.quick-chat')
    @endif

    @livewireScripts
    @stack('scripts')
</body>
</html>
