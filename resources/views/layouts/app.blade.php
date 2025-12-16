<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Medical Check Up | PJT 1</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('vuexy/assets/img/favicon/icon.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/dropzone/dropzone.css') }}" />

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/node-waves/node-waves.css') }}" />

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <!-- endbuild -->

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/quill/typography.css') }}" />

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/select2/select2.css') }}" />
    {{-- <link rel="stylesheet" --}}
    {{-- href="{{ asset('vuexy/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" /> --}}
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/pages/cards-advance.css') }}" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/css/datatables-custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy/assets/css/new-datatables.css') }}" />


    <!-- Helpers -->
    <script src="{{ asset('vuexy/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <script src="{{ asset('vuexy/assets/vendor/js/template-customizer.js') }}"></script>   
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{ asset('vuexy/assets/js/config.js') }}"></script>
    {{-- CKeditor di Head --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .card.h-100 {
            transition: all 0.3s ease;
            /* smooth animation */
        }

        .card.h-100:hover {
            transform: scale(1.02);
            /* expand a bit */
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
            /* blue glow */
            z-index: 2;
            /* bring it to front */
        }

        .select2-container .select2-selection__rendered {
            white-space: nowrap !important;
        }

        .select2-container {
            min-width: 110px !important;
        }
    </style>

    @stack('style')

    @livewireStyles
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.sidebar')
            <div class="layout-page">
                <!-- Navbar -->
                @include('layouts.nav')
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    @include('layouts.footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
        </div>
    </div>

    @stack('modals')

    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js -->

    <script src="{{ asset('vuexy/assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/hammer/hammer.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/libs/i18n/i18n.js') }}"></script>

    <script src="{{ asset('vuexy/assets/vendor/js/menu.js') }}"></script>


    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('vuexy/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('vuexy/assets/js/forms-file-upload.js') }}"></script>

    <!-- Main JS -->

    <script src="{{ asset('vuexy/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('vuexy/assets/js/dashboards-analytics.js') }}"></script>




    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    @if (session()->has('success'))
        <script>
            var notyf = new Notyf({
                dismissible: true
            })
            notyf.success('{{ session('success') }}')
        </script>
    @endif

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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            var table = $('#usersTable').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#categoriesTable').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#subCategoriesTable').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#typepemohonsTable').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#typeLandingsTable').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#subunitsTable').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#rolesTable').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#permissionsTable').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#annual-tanda-vital-1').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#annual-tanda-vital-2').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#komponen-darah-1').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#komponen-darah-2').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#komponen-darah-3').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#komponen-darah-4').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#kimia-darah-1').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#kimia-darah-2').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#kimia-darah-3').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#kimia-darah-4').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#urin-rutin-1').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#urin-rutin-11').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#urin-rutin-2').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#urin-rutin-3').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#urin-rutin-4').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#urin-rutin-5').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#kesimpulan-saran').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#non-lab-1').DataTable({
                responsive: true // Mengaktifkan responsif
            });
            var table = $('#non-lab-2').DataTable({
                responsive: true // Mengaktifkan responsif
            });
        });
    </script>
    <script>
        const select2 = $('.select2')
        if (select2.length) {
            select2.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select value',
                    dropdownParent: $this.parent()
                });
            });
        }
    </script>
    <script>
        // Call this after DataTable init
        function syncDtThemeClass() {
            // determine theme: prefer data-bs-theme on html/body, then .dark-layout class
            const doc = document.documentElement;
            const body = document.body;
            let theme = 'light';
            if (doc.getAttribute('data-bs-theme') === 'dark' || body.getAttribute('data-bs-theme') === 'dark') theme =
                'dark';
            if (doc.classList.contains('dark-layout') || body.classList.contains('dark-layout')) theme = 'dark';

            document.querySelectorAll('.dataTables_wrapper').forEach(w => {
                if (theme === 'dark') {
                    w.classList.remove('dt-light');
                    w.classList.add('dt-dark');
                } else {
                    w.classList.remove('dt-dark');
                    w.classList.add('dt-light');
                }
            });
        }

        // Run once after DOM ready & after DataTables init
        $(document).ready(function() {
            // If you init DataTables here, do it first. Example:
            $('.datatables-basic').each(function() {
                // if not yet initialised (protect double init)
                if (!$.fn.dataTable.isDataTable(this)) {
                    $(this).DataTable({
                        responsive: true,
                        // ... your options here
                    });
                }
            });

            // initial sync
            syncDtThemeClass();

            // observe changes on <html> and <body> attributes or classlist (to detect Vuexy theme toggle)
            const observer = new MutationObserver(mutations => {
                // any change => resync
                syncDtThemeClass();
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-bs-theme', 'class']
            });
            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['data-bs-theme', 'class']
            });

            // optional: re-run when datatable draws (useful if datatable replaced DOM)
            $(document).on('draw.dt', function() {
                syncDtThemeClass();
            });
        });
    </script>
    <script>
        function getVuexyThemeColors() {
            const style = getComputedStyle(document.documentElement);
            return {
                text: style.getPropertyValue('--bs-body-color').trim(),
                grid: style.getPropertyValue('--bs-border-color').trim(),
                background: style.getPropertyValue('--bs-body-bg').trim(),
            };
        }
    </script>

    @stack('scripts')
</body>

</html>
