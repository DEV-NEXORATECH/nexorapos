@extends('admin.layout.app')

@section('title', 'Profile')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Profile</h4>

            {{-- Update Profile Information --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Update Profile Information</div>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card mt-4">
                <div class="card-header">
                    <div class="card-title">Update Password</div>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="card mt-4">
                <div class="card-header">
                    <div class="card-title">Delete Account</div>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
