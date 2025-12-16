@extends('errors.layout')

@section('title', '403 Forbidden')

@section('content')
    <div class="misc-wrapper text-center">
        <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem;">403</h1>
        <h4 class="mb-2 mx-2">Access Forbidden 🚫</h4>
        <p class="mb-6 mx-2">Sorry, you don’t have permission to access this page.</p>
        <a href="{{ url('/') }}" class="btn btn-primary mb-10">Back to home</a>
        <div class="mt-4">
            <img src="{{ asset('vuexy/assets/img/illustrations/page-misc-you-are-not-authorized.png') }}" alt="Forbidden Illustration"
                width="225" class="img-fluid" />
        </div>
    </div>

@endsection
