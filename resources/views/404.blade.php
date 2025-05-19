@extends('user.layouts.client')

@section('title', '404 - Page Not Found')

@section('content')
    <div style="min-height: 60vh; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <h1 style="font-size: 4rem; color: #dc3545;">404</h1>
        <h2>Page Not Found</h2>
        <p>Trang bạn tìm kiếm không tồn tại hoặc đã bị di chuyển.</p>
        <button onclick="window.history.back()" class="btn btn-primary mt-3">
            Quay lại trang trước
        </button>
    </div>
@endsection
