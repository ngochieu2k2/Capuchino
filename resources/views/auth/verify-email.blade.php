@extends('user.layouts.client')
@section('content')
    @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
        <div class="container mt-5" style="padding:50px">
            <h2>Vui lòng xác minh địa chỉ email của bạn</h2>
            <p>
                Một liên kết xác minh đã được gửi tới email của bạn.<br>
                Nếu bạn không nhận được, hãy nhấp vào nút bên dưới để yêu cầu nhận lại.
            </p>
            @if (session('message'))
                <div class="alert alert-success mt-2">{{ session('message') }}</div>
            @endif
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary mt-3">Gửi lại Email xác minh</button>
            </form>
        </div>
    @else
        <h3 class="text-center mt-5">Tài khoản đã được xác minh</h3>
    @endif

@endsection
