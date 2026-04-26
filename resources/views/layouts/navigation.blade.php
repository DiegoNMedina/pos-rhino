<nav x-data="{ open: false, dark: document.documentElement.classList.contains('dark'), toggleTheme() { this.dark = !this.dark; document.documentElement.classList.toggle('dark', this.dark); localStorage.setItem('theme', this.dark ? 'dark' : 'light'); } }" class="bg-white/80 backdrop-blur border-b border-gray-100 sticky top-0 z-40 dark:bg-gray-950/80 dark:border-gray-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <div class="flex items-center gap-3">
                            <x-application-logo class="block h-9 w-auto text-indigo-600" />
                            <div class="leading-tight">
                                <div class="text-sm font-semibold text-gray-900">{{ config('app.name') }}</div>
                                <div class="text-xs text-gray-500">Punto de venta</div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Inicio
                    </x-nav-link>
                    <x-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                        POS
                    </x-nav-link>
                    @can('manage-platform')
                        <x-nav-link :href="route('platform.dashboard')" :active="request()->routeIs('platform.*')">
                            Plataforma
                        </x-nav-link>
                    @endcan
                    @can('manage-support')
                        <x-nav-link :href="route('support.admin.index')" :active="request()->routeIs('support.admin.*')">
                            Soporte
                        </x-nav-link>
                    @endcan
                    @can('manage-pos')
                        <div class="flex items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-900 hover:border-gray-300 focus:outline-none focus:text-gray-900 focus:border-gray-300 transition duration-150 ease-in-out dark:text-gray-300 dark:hover:text-white dark:hover:border-gray-700 dark:focus:text-white dark:focus:border-gray-700">
                                        <div>Administración</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.dashboard')">
                                        Panel
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.settings.edit')">
                                        Configuración
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.products.index')">
                                        Productos
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.customers.index')">
                                        Clientes
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.sales.index')">
                                        Ventas
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.reports.index')">
                                        Reportes
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.users.index')">
                                        Usuarios
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <button type="button" x-on:click="toggleTheme()" class="inline-flex items-center justify-center h-10 w-10 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition dark:bg-gray-950 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white">
                    <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 3a1 1 0 011 1v1a1 1 0 11-2 0V4a1 1 0 011-1Zm0 11a4 4 0 100-8 4 4 0 000 8Zm7-4a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1ZM6 10a1 1 0 01-1 1H4a1 1 0 110-2h1a1 1 0 011 1Zm9.364 5.364a1 1 0 01-1.414 0l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 010 1.414ZM6.05 6.05a1 1 0 01-1.414 0l-.707-.707A1 1 0 115.343 3.93l.707.707a1 1 0 010 1.414Zm9.9-1.414a1 1 0 010 1.414l-.707.707A1 1 0 1113.83 5.343l.707-.707a1 1 0 011.414 0ZM6.05 13.95a1 1 0 010 1.414l-.707.707A1 1 0 113.93 13.83l.707-.707a1 1 0 011.414 0ZM10 15a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1Z" />
                    </svg>
                    <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586Z" />
                    </svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150 dark:bg-gray-950 dark:text-gray-300 dark:hover:text-white">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Perfil
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('billing.index')">
                            Facturación
                        </x-dropdown-link>
                        @can('manage-support')
                            <x-dropdown-link :href="route('support.admin.index')">
                                Soporte
                            </x-dropdown-link>
                        @else
                            <x-dropdown-link :href="route('support.chat')">
                                Soporte
                            </x-dropdown-link>
                        @endcan

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Salir
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden gap-2">
                <button type="button" x-on:click="toggleTheme()" class="inline-flex items-center justify-center h-10 w-10 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition dark:bg-gray-950 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white">
                    <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 3a1 1 0 011 1v1a1 1 0 11-2 0V4a1 1 0 011-1Zm0 11a4 4 0 100-8 4 4 0 000 8Zm7-4a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1ZM6 10a1 1 0 01-1 1H4a1 1 0 110-2h1a1 1 0 011 1Zm9.364 5.364a1 1 0 01-1.414 0l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 010 1.414ZM6.05 6.05a1 1 0 01-1.414 0l-.707-.707A1 1 0 115.343 3.93l.707.707a1 1 0 010 1.414Zm9.9-1.414a1 1 0 010 1.414l-.707.707A1 1 0 1113.83 5.343l.707-.707a1 1 0 011.414 0ZM6.05 13.95a1 1 0 010 1.414l-.707.707A1 1 0 113.93 13.83l.707-.707a1 1 0 011.414 0ZM10 15a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1Z" />
                    </svg>
                    <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586Z" />
                    </svg>
                </button>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Inicio
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.*')">
                POS
            </x-responsive-nav-link>
            @can('manage-platform')
                <x-responsive-nav-link :href="route('platform.dashboard')" :active="request()->routeIs('platform.*')">
                    Plataforma
                </x-responsive-nav-link>
            @endcan
            @can('manage-support')
                <x-responsive-nav-link :href="route('support.admin.index')" :active="request()->routeIs('support.admin.*')">
                    Soporte
                </x-responsive-nav-link>
            @endcan
            @can('manage-pos')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    Administración
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.settings.edit')" :active="request()->routeIs('admin.settings.*')">
                    Configuración
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                    Productos
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">
                    Clientes
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.sales.index')" :active="request()->routeIs('admin.sales.*')">
                    Ventas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                    Reportes
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    Usuarios
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Perfil
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('billing.index')">
                    Facturación
                </x-responsive-nav-link>
                @can('manage-support')
                    <x-responsive-nav-link :href="route('support.admin.index')">
                        Soporte
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('support.chat')">
                        Soporte
                    </x-responsive-nav-link>
                @endcan

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Salir
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
