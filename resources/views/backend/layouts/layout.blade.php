<!doctype html>

<html lang="es">


<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="http://localhost:8081/tienda_perseo">
    {{-- <meta name="file-base-url" content="{{ getFileBaseURL() }}"> --}}

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicon -->
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/jpg;base64,{{ get_setting('icono_sitio') }}">
    <title>Tienda Perseo</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- google font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

    <!-- aiz core css -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css')}}">

    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css') }}">

    <style>
        body {
            font-size: 12px;
        }
    </style>
    <script>
        var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: 'Nada Seleccionado',
            nothing_found: 'Nada encontrado',
            choose_file: 'Seleccionar Archivo',
            file_selected: 'Archivo Seleccionado',
            files_selected: 'Archivos Seleccionados',
            add_more_files: 'Agrega Mas Archivos',
            adding_more_files: 'Agregar Mas Archivos',
            drop_files_here_paste_or: 'Arrastar archivos o pegarlos',
            browse: 'Buscar',
            upload_complete: 'Carga Completa',
            upload_paused: 'Carga Pausada',
            resume_upload: 'Reanudar Carga',
            pause_upload: 'Pausar Carga',
            retry_upload: 'Reintentar la Carga',
            cancel_upload: 'Cancelar Carga',
            uploading: 'Subiendo',
            processing: 'Procesando',
            complete: 'Completado',
            file: 'Archivo',
            files: 'Archivos',
    }
    </script>

</head>

<body class="">

    <div class="aiz-main-wrapper d-flex">
        <div class="flex-grow-1">
            @yield('content')
        </div>
    </div><!-- .aiz-main-wrapper -->

    @yield('modal')


    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
    <script src="{{ static_asset('assets/js/aiz-core.js') }}"></script>

    @yield('script')

    <script type="text/javascript">
        @foreach (session('flash_notification', collect())->toArray() as $message)
            AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
        @endforeach
    </script>

</body>

</html>