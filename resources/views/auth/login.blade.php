<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Medical Check Up | PJT 1</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('vuexy/assets/img/ppid-icon.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com') }}" />
    <link rel="preconnect" href="https://fonts.gstatic.com') }}" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/node-waves/node-waves.css') }}" />

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/pickr/pickr-themes.css') }}" />

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- endbuild -->

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/@form-validation/form-validation.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('vuexy/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('vuexy/assets/vendor/js/template-customizer.js') }}"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{ asset('vuexy/assets/js/config.js') }}"></script>

    {{-- @livewireStyles --}}
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <!-- Login -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <a href="{{ url('/') }}" class="app-brand-link">
                                <span class="app-brand-logo demo">
                                    <span class="text-primary">
                                        <img src="{{ asset('vuexy/assets/img/cropped-LogoPPID-PJT1.png') }}"
                                            alt="" width="150" />
                                    </span>
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1">Welcome to MCU! 👋</h4>
                        <p class="mb-6">Please sign-in to your account and see your Medical Check Up</p>
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form class="mb-4" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-6 form-control-validation">
                                <label for="kopeg" class="form-label">Username</label>

                                <input id="kopeg" type="kopeg"
                                    class="form-control @error('kopeg') is-invalid @enderror" name="kopeg"
                                    value="{{ old('kopeg') }}" required autocomplete="kopeg" autofocus>

                                @error('kopeg')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-6 form-password-toggle form-control-validation">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base ti tabler-eye-off"></i></span>
                                </div>
                            </div>
                            <div class="my-8">
                                <div class="d-flex justify-content-between">
                                    <div class="form-check mb-0 ms-2">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-6">
                                <button class="btn btn-primary d-grid w-100"
                                    type="submit">{{ __('Login') }}</button>
                            </div>
                        </form>

                    </div>
                </div>
                <!-- /Login -->
            </div>
        </div>
    </div>
    @stack('modals')

    @livewireScripts

    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js -->

    <script src="{{ asset('vuexy/assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/hammer/hammer.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/i18n/i18n.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('vuexy/assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

    <!-- Main JS -->

    <script src="{{ asset('vuexy/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('vuexy/assets/js/pages-auth.js') }}"></script>

    <script>
        /* Simple Alpine Image Viewer */
        document.addEventListener('alpine:init', () => {
            Alpine.data('imageViewer', (src = '') => {
                return {
                    imageUrl: src,

                    refreshUrl() {
                        this.imageUrl = this.$el.getAttribute("image-url")
                    },

                    fileChosen(event) {
                        this.fileToDataUrl(event, src => this.imageUrl = src)
                    },

                    fileToDataUrl(event, callback) {
                        if (!event.target.files.length) return

                        let file = event.target.files[0],
                            reader = new FileReader()

                        reader.readAsDataURL(file)
                        reader.onload = e => callback(e.target.result)
                    },
                }
            })
        })
    </script>
</body>

</html>
