@extends('layouts.front')

@section('styles')
@endsection

@section('content')

<div
    id="pizzachart"
    data-titulo="{{ json_encode($title) }}"
    data-format="{{ json_encode($format) }}"
>
</div>
   
@endsection

@section('scripts')
    @viteReactRefresh
    @vite('resources/js/react/views/PizzaView.jsx')
@endsection
