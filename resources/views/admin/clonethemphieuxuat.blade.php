@extends('admin.layouts.client')
@section('title')
    <title>[Admin] - Phiếu xuất</title>
@endsection
@section('head')
    {{-- thêm css --}}
@endsection
@section('content')
    <h1><a href="{{ url('/') }}">Trang chủ</a></h1>
    <h1><a href="{{ url('themphieuxuat') }}">Thêm phiếu xuất</a></h1>
    <hr>
    <hr>
    <hr>
@endsection
@section('js')
    {{-- thêm js --}}
@endsection
