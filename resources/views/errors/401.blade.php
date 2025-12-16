@extends('errors.layout')

@section('title', '401 Not Authorized')

@section('content')
    <div class="misc-wrapper text-center">
        <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem;">401</h1>
        <h4 class="mb-2 mx-2">You are not authorized! 🔐</h4>
        <p class="mb-6 mx-2">You don’t have permission to access this page. Go Home!</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Back to home</a>
        <div class="mt-4">
            <img src="{{ asset('vuexy/assets/img/illustrations/page-misc-you-are-not-authorized.png') }}" alt="Not authorized"
                width="170" class="img-fluid" />
        </div>
    </div>
@endsection
