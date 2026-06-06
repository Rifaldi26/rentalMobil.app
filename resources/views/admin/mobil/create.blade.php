@extends('layouts.admin')
@section('title', 'Tambah Mobil')
@section('page-title', 'Tambah Mobil')

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush

@section('content')
<div class="admin-content">
    @include('admin.mobil._form')
</div>
@endsection

@push('scripts')
    @vite(['resources/js/admin/mobil.js'])
@endpush