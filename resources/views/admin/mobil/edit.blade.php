@extends('layouts.admin')
@section('title', 'Edit Mobil')
@section('page-title', 'Edit Mobil')

@section('content')
<div class="admin-content">
    <div class="mobil-form-wrap">
        @include('admin.mobil._form')
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/admin/mobil.js'])
@endpush
