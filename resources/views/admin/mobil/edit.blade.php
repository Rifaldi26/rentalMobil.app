@extends('layouts.admin')
@section('title', 'Edit Mobil')
@section('page-title', 'Edit Mobil')

@push('styles')
    @vite(['resources/css/pemesanan.css'])
@endpush

@section('content')
<div class="admin-content">
    @include('admin.mobil._form')
</div>
@endsection

@push('scripts')
    @vite(['resources/js/admin/mobil.js'])
@endpush