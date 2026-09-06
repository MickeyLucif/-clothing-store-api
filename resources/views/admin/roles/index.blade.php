@extends('layouts.app')

@section('title', 'Roles & permissions')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/roles.css') }}">
@endpush

@section('header')
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="mb-0">Roles & permissions</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Roles</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @php

    @endphp

    <div class="row">
        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Roles</h3>
                    <button type="button" class="btn btn-primary btn-sm ms-auto" disabled>
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>New role
                    </button>
                </div>

                <div class="card-body p-2">
                    <form method="GET" action="{{ route('admin.roles.index') }}" class="row g-2 mb-3">
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                                <input
                                    type="search"
                                    name="query"
                                    class="form-control"
                                    value="{{ $query }}"
                                    placeholder="Search by role name"
                                >
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <label class="input-group-text" for="role-filter-guard">Guard</label>
                                <select id="role-filter-guard" name="guard" class="form-select">
                                    <option value="all" @selected($guard === 'all')>All</option>
                                    @foreach (\App\Enum\Guard::cases() as $guardOption)
                                        <option value="{{ $guardOption->value }}" @selected($guard === $guardOption->value)>
                                            {{ ucfirst($guardOption->value) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="list-group list-group-flush">
                        @forelse ($roles as $role)
                            <a
                                href="{{ route('admin.roles.index', ['query' => $query ?: null, 'guard' => $guard, 'role' => $role->id]) }}"
                                class="role-item list-group-item list-group-item-action d-flex align-items-center {{ $selectedRole?->is($role) ? 'active' : '' }}"
                            >
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 fw-semibold">
                                        <span>{{ $role->name }}</span>
                                        <span class="badge rounded-pill role-guard-badge {{ $selectedRole?->is($role) ? 'text-bg-light text-primary' : 'text-bg-secondary' }}">
                                            {{ $role->guard_name }}
                                        </span>
                                    </div>
                                    <small class="{{ $selectedRole?->is($role) ? 'text-white-50' : 'text-body-secondary' }}">
                                        {{ $role->permissions_count }} permissions
                                    </small>
                                </div>
                            </a>
                        @empty
                            <div class="p-3 text-center text-body-secondary">No roles found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title mb-0">{{ $selectedRole?->name ?? 'Select a role' }}</h3>
                </div>

                <div class="card-body">
                    @if ($selectedRole)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role-name" class="form-label">Name</label>
                                <input id="role-name" class="form-control" value="{{ $selectedRole->name }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="role-guard" class="form-label">Guard</label>
                                <input id="role-guard" class="form-control" value="{{ $selectedRole->guard_name }}" disabled>
                            </div>
                        </div>

                        <div class="border-top pt-3 mb-2">
                            <h4 class="h6 mb-1">Permission matrix</h4>
                            <p class="small text-body-secondary mb-0">Select permissions for this role.</p>
                        </div>

                        <div class="table-responsive permission-matrix-wrapper">
                            <table class="table table-hover permission-table permission-matrix mb-0">
                                <thead>
                                    <tr>
                                        <th class="module-column">Module</th>
                                        @foreach ($actions as $action)
                                            <th class="permission-cell">
                                                <span class="permission-action-label">{{ ucfirst($action) }}</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($permissionModules as $module => $modulePermissions)
                                        <tr>
                                            <td class="module-column">
                                                <div class="d-flex align-items-center">
                                                    <span class="module-icon me-2">
                                                        <i class="bi bi-grid" aria-hidden="true"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-semibold">{{ ucfirst($module) }}</div>
                                                        <small class="text-body-secondary">{{ $modulePermissions->count() }} permissions</small>
                                                    </div>
                                                </div>
                                            </td>

                                            @foreach ($actions as $action)
                                                <td class="permission-cell">
                                                    @if ($modulePermissions->contains(fn ($permission) => str($permission->name)->after('.')->toString() === $action))
                                                        <input type="checkbox" class="form-check-input permission-checkbox">
                                                    @else
                                                        <span class="permission-unavailable" aria-label="Not available">
                                                            <i class="bi bi-dash-lg" aria-hidden="true"></i>
                                                        </span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-body-secondary">No permissions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-body-secondary py-5">Create a role to begin.</div>
                    @endif
                </div>

                <div class="card-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" disabled>Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" disabled>Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
