@extends('errors.layout')

@section('title', '404 Page Not Found')

@section('content')
    <div class="misc-wrapper text-center">
        <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem;">500</h1>
        <h4 class="mb-2 mx-2">An Error Occurred ⚠️</h4>
        <p class="mb-6 mx-2">Something went wrong, please try again later.</p>
        <a href="{{ url('/') }}" class="btn btn-primary mb-10">Back to home</a>
        <div class="mt-4">
            <img src="{{ asset('vuexy/assets/img/illustrations/page-misc-error.png') }}" alt="Error illustration"
                width="225" class="img-fluid" />
        </div>
    </div>
@endsection
