<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Budgets') · Finance Control</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @stack('head')
    <style>
        [x-cloak] { display: none !important; }

        body {
            background: #f5f7fb;
        }

        .toastr-custom {
            border-radius: 1rem;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
        }
    </style>
</head>
<body class="font-sans text-slate-900 antialiased" x-data="{ sidebarOpen: false }">
    <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"></div>

    <div class="min-h-screen lg:flex">
        <aside x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 flex w-72 shrink-0 -translate-x-full flex-col bg-slate-950 px-6 py-7 text-slate-100 transition-transform duration-200 lg:static lg:min-h-screen lg:translate-x-0">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500/20 text-indigo-300">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.28em] text-slate-400">Personal finance</p>
                    <p class="text-lg font-semibold tracking-tight">Finance Control</p>
                </div>
            </div>

            <div class="mt-12">
                <p class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">Espace de travail</p>
                <a href="{{ route('budgets.index') }}"
                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('budgets.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                    <span>Gestion des budgets</span>
                </a>
                <a href="{{ route('depense-previsions.index') }}"
                    class="mt-1 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('depense-previsions.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                    <span>Prévisions de dépenses</span>
                </a>
                <a href="{{ route('revenu-previsions.index') }}"
                    class="mt-1 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('revenu-previsions.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="fa-solid fa-arrow-trend-up w-5 text-center"></i>
                    <span>Prévisions de revenus</span>
                </a>
            </div>

            <div class="mt-auto rounded-3xl border border-white/10 bg-white/5 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-500 text-sm font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->prenom, 0, 1) . substr(auth()->user()->nom, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</p>
                        <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <a href="/" class="mt-4 flex items-center gap-2 text-xs font-medium text-slate-400 transition hover:text-white">
                    <i class="fa-solid fa-arrow-left"></i>
                    Retour à l’accueil
                </a>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white">
                <div class="flex h-20 items-center justify-between gap-4 px-4 sm:px-7 lg:px-10">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" @click="sidebarOpen = true"
                            class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm lg:hidden"
                            aria-label="Ouvrir le menu">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div class="min-w-0">
                            <p class="hidden text-xs font-medium uppercase tracking-[0.18em] text-slate-400 sm:block">Gestion des finances personnelles</p>
                            <h1 class="truncate text-xl font-bold tracking-tight text-slate-950">@yield('page_title', 'Budgets')</h1>
                        </div>
                    </div>
                    <div class="hidden items-center gap-3 text-sm text-slate-500 sm:flex">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <i class="fa-regular fa-calendar"></i>
                        </span>
                        <span>{{ now()->format('d/m/Y') }}</span>
                    </div>
                </div>
            </header>

            <main class="px-4 py-8 sm:px-7 lg:px-10">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('js/budgets.js') }}"></script>
    <script src="{{ asset('js/previsions-depenses.js') }}"></script>
    <script src="{{ asset('js/previsions-revenus.js') }}"></script>

    @if (session('success'))
        <script>
            window.addEventListener('load', () => window.BudgetUI.notify('success', @json(session('success'))));
        </script>
    @endif

    @if (session('error'))
        <script>
            window.addEventListener('load', () => window.BudgetUI.notify('error', @json(session('error'))));
        </script>
    @endif

    @if ($errors->any())
        <script>
            window.addEventListener('load', () => window.BudgetUI.notify('error', 'Veuillez corriger les champs signalés.'));
        </script>
    @endif

    @stack('scripts')
</body>
</html>
