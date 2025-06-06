@extends('layouts.front')

@section('styles')
@endsection

@section('content')
    <div
        id="agence"
        data-users="{{ json_encode($users) }}"
        data-fecha-inicio-cookie="{{ Cookie::get('fecha_inicio') }}"
        data-fecha-fin-cookie="{{ Cookie::get('Fecha_fin') }}"
        data-csrf-token="{{ csrf_token() }}"
    >
    </div>
@endsection

@section('scripts')
    @viteReactRefresh
    @vite('resources/js/react/views/AgenceView.jsx')
@endsection