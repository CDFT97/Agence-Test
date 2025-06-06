@extends('layouts.front')

@section('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">

    <style>
        .ui-datepicker-calendar {
            display: none;
        }
    </style>
@endsection

@section('content')
        <div 
            id="inform" 
            data-titulo="{{ json_encode($titulo) }}"
            data-data="{{ json_encode($datosFormateados) }}"
        ></div>
@endsection

@section('scripts')
    @viteReactRefresh
    @vite('resources/js/react/views/InformView.jsx')
@endsection
