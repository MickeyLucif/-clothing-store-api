@foreach ($items as $item)
    @if (($item['type'] ?? 'link') === 'header')
        <li class="nav-header">{{ $item['label'] }}</li>
    @elseif (($item['type'] ?? 'link') === 'group')
        @php
            $isActive = collect($item['active'] ?? [])->contains(
                fn (string $pattern): bool => request()->routeIs($pattern),
            );
        @endphp
        <li class="nav-item {{ $isActive ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ $isActive ? 'active' : '' }}">
                <i class="nav-icon {{ $item['icon'] ?? 'bi bi-circle' }}" aria-hidden="true"></i>
                <p>
                    {{ $item['label'] }}
                    <i class="nav-arrow bi bi-chevron-right" aria-hidden="true"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @include('layouts.partials.sidebar-items', ['items' => $item['items'] ?? []])
            </ul>
        </li>
    @else
        @php
            $isActive = collect($item['active'] ?? [])->contains(
                fn (string $pattern): bool => request()->routeIs($pattern),
            );
            $href = isset($item['route']) ? route($item['route']) : ($item['url'] ?? '#');
        @endphp
        <li class="nav-item">
            <a href="{{ $href }}" class="nav-link {{ $isActive ? 'active' : '' }}">
                <i class="nav-icon {{ $item['icon'] ?? 'bi bi-circle' }}" aria-hidden="true"></i>
                <p>{{ $item['label'] }}</p>
            </a>
        </li>
    @endif
@endforeach
