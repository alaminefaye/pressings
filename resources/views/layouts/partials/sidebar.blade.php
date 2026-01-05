<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Pressing App</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <!-- Gestion -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Gestion</span>
        </li>

        <!-- Orders -->
        <li class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <a href="{{ route('admin.orders.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cart"></i>
                <div data-i18n="Commandes">Commandes</div>
            </a>
        </li>

        <!-- Customers -->
        <li class="menu-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <a href="{{ route('admin.customers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Clients">Clients</div>
            </a>
        </li>

        <!-- Employees -->
        <li class="menu-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
            <a href="{{ route('admin.employees.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div data-i18n="Employés">Employés</div>
            </a>
        </li>

        <!-- Livraison -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Livraison</span>
        </li>

        <!-- Drivers -->
        <li class="menu-item {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
            <a href="{{ route('admin.drivers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-car"></i>
                <div data-i18n="Livreurs">Livreurs</div>
            </a>
        </li>

        <!-- Deliveries -->
        <li class="menu-item {{ request()->routeIs('admin.deliveries.*') ? 'active' : '' }}">
            <a href="{{ route('admin.deliveries.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div data-i18n="Livraisons">Livraisons</div>
            </a>
        </li>

        <!-- Catalog -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Catalogue</span>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
            <a href="{{ route('admin.services.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cube"></i>
                <div data-i18n="Services">Services</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.clothing-types.*') ? 'active' : '' }}">
            <a href="{{ route('admin.clothing-types.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-closet"></i>
                <div data-i18n="Types de Vêtements">Types de Vêtements</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.prices.*') ? 'active' : '' }}">
            <a href="{{ route('admin.prices.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dollar"></i>
                <div data-i18n="Tarifs">Tarifs</div>
            </a>
        </li>

        <!-- Promotions -->
        <li class="menu-item {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
            <a href="{{ route('admin.promotions.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-gift"></i>
                <div data-i18n="Promotions">Promotions</div>
            </a>
        </li>

        <!-- Reports & Settings -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Système</span>
        </li>

        <!-- Reports -->
        <li class="menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reports.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bar-chart"></i>
                <div data-i18n="Rapports">Rapports</div>
            </a>
        </li>

        <!-- Settings -->
        <li class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div data-i18n="Paramètres">Paramètres</div>
            </a>
        </li>

        <li class="menu-item">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <a href="#" class="menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="menu-icon tf-icons bx bx-power-off"></i>
                    <div data-i18n="Déconnexion">Déconnexion</div>
                </a>
            </form>
        </li>
    </ul>
</aside>

