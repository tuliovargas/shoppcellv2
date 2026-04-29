@extends('adminlte::page')

@section('title', 'Home')

@section('content')


<status-cards></status-cards>
@if (Auth::user()->hasRole('admin'))
    <graphic></graphic>
@endif
    
@stop


@section('js')
<script src="{{ mix('js/module-dashboard.js') }}"></script>
@stop
