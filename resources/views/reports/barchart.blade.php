@extends('layouts.front')

@section('styles')
@endsection

@section('content')

<div
    id="barchart"
    data-titulo="{{ json_encode($tituloGrafica) }}"
    data-format="{{ json_encode($formatoGraficaCosUsuario) }}"
    data-meses="{{ json_encode($mesesName) }}">
</div>


@endsection

@section('scripts')
@viteReactRefresh
@vite('resources/js/react/views/BarchartView.jsx')

@endsection