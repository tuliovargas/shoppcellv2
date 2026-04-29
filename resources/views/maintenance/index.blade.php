@extends('adminlte::page')

@section('title', 'Manutenção')

@section('content')
    <maintenance-index user-is-admin="{{\Illuminate\Support\Facades\Auth::user()->hasRole('admin')}}"></maintenance-index>
@stop

@section('js')
    <script src="{{asset('js/app.js')}}"></script>
@stop
