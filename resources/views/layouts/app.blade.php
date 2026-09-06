<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin dashboard') | {{ config('app.name', 'Laravel') }}</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('AdminLTE-master/dist/css/adminlte.min.css') }}">
        @stack('styles')
    </head>
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <div class="app-wrapper">
            <nav class="app-header navbar navbar-expand bg-body">
                <div class="container-fluid">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <button class="nav-link" type="button" data-lte-toggle="sidebar" aria-label="Toggle navigation">
                                <span aria-hidden="true">Menu</span>
                            </button>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item px-2 text-body-secondary">
                            {{ Auth::guard('admin')->user()->name }}
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-secondary btn-sm" type="submit">
                                    Log out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
                <div class="sidebar-brand">
                    <a href="{{ route('admin.dashboard') }}" class="brand-link text-decoration-none">
                        <span class="brand-text fw-light">{{ config('admin-sidebar.brand') }}</span>
                    </a>
                </div>

                <div class="sidebar-wrapper">
                    <nav class="mt-2">
                        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" data-accordion="false">
                            @include('layouts.partials.sidebar-items', ['items' => config('admin-sidebar.items', [])])
                        </ul>
                    </nav>
                </div>
            </aside>

            <main class="app-main">
                @hasSection('header')
                    <div class="app-content-header">
                        <div class="container-fluid">
                            @yield('header')
                        </div>
                    </div>
                @endif

                <div class="app-content">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
            </main>

            <footer class="app-footer">
                <strong>{{ config('admin-sidebar.brand') }}</strong>
            </footer>
        </div>

        <script src="{{ asset('AdminLTE-master/dist/js/adminlte.min.js') }}"></script>
        @stack('scripts')
    </body>
</html>
