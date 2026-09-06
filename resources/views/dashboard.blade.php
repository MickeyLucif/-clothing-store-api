@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="mb-0">{{ 'Dashboard' }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end mb-0">
                <li class="breadcrumb-item active" aria-current="page">{{ 'Dashboard' }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ 'Administration' }}</h3>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ "You're logged in as an administrator." }}</p>
        </div>
    </div>
@endsection
