<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\FrontEnd\{
    GioHangController,
    SanPhamController,
    TaiKhoanController,
    ThanhToanController,
    TrangChuController
};
use App\Http\Controllers\BackEnd\{
    AdminController,
    CouponController,
    XulyHangsanxuat,
    XulyLaptop,
    XulyMagiamgia,
    XulyNguoidung,
    XulyPhieuxuat,
    XulyPhieunhap,
    XulyPhukien,
    XulySanpham,
    XulyTongquan,
    FeedBackController
};
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// =========================== FRONTEND ===========================
Route::group(['namespace' => 'FrontEnd'], function () {
    // Trang chủ
    Route::get('/', [TrangChuController::class, 'trangchu'])->name('/');
    Route::get('/tragop', [TrangChuController::class, 'tragop'])->name('tragop');
    Route::get('/baohanh', [TrangChuController::class, 'baohanh'])->name('baohanh');
    Route::get('/lienhe', [TrangChuController::class, 'lienhe'])->name('lienhe');
    Route::post('/xulylienhe', [TrangChuController::class, 'xulylienhe'])->name('xulylienhe');

    // Sản phẩm
    Route::get('chitietsp', [SanPhamController::class, 'chitietsp'])->name('chitietsp');
    Route::get('danhsachsp', [SanPhamController::class, 'danhsachsp'])->name('danhsachsp');
    Route::get('timkiem', [SanPhamController::class, 'timkiem'])->name('timkiem');

    // Tài khoản - Đăng nhập / Đăng ký
    Route::middleware('guest')->group(function () {
        Route::get('dangnhap', [TaiKhoanController::class, 'dangnhap'])->name('login');
        Route::post('xulytaikhoan', [TaiKhoanController::class, 'xulytaikhoan'])->name('xulytaikhoan');
    });

    Route::middleware('auth')->group(function () {
        Route::get('dangxuat', [TaiKhoanController::class, 'dangxuat'])->name('dangxuat');

        // Các route cần xác thực email
        Route::middleware('verified')->group(function () {
            Route::get('taikhoan', [TaiKhoanController::class, 'taikhoan'])->name('taikhoan');
            Route::get('giohang', [TrangChuController::class, 'giohang'])->name('giohang');
            Route::get('yeuthich', [TrangChuController::class, 'yeuthich'])->name('yeuthich');
            Route::get('thanhtoan', [ThanhToanController::class, 'thanhtoan'])->name('thanhtoan');

            Route::post('xulygiohang', [GioHangController::class, 'xulygiohang'])->name('xulygiohang');
            Route::post('ap-dung-ma-giam-gia', [GioHangController::class, 'applyCoupon'])->name('apply.coupon');
            Route::post('xulythanhtoan', [ThanhToanController::class, 'xulythanhtoan'])->name('xulythanhtoan');
            Route::post('ap-dung-ma-giam-gia', [ThanhToanController::class, 'applyCoupon'])->name('apply.coupon');
        });
    });
});

// =========================== BACKEND ===========================
Route::prefix('admin')->namespace('BackEnd')->middleware('admin.role')->group(function () {
    Route::get('/tongquan', [XulyTongquan::class, 'tongquan'])->name('tongquan');

    Route::get('/laptop', [AdminController::class, 'laptop'])->name('laptop');
    Route::post('/xulylaptop', [XulyLaptop::class, 'xulylaptop'])->name('xulylaptop');

    Route::get('/phukien', [AdminController::class, 'phukien'])->name('phukien');
    Route::post('/xulyphukien', [XulyPhukien::class, 'xulyphukien'])->name('xulyphukien');

    Route::get('/hangsanxuat', [AdminController::class, 'hangsanxuat'])->name('hangsanxuat');
    Route::post('/xulyhangsanxuat', [XulyHangsanxuat::class, 'xulyhangsanxuat'])->name('xulyhangsanxuat');

    Route::get('/phieuxuat', [AdminController::class, 'phieuxuat'])->name('phieuxuat');
    Route::get('/xemphieuxuat', [AdminController::class, 'xemphieuxuat'])->name('xemphieuxuat');
    Route::post('/xulyphieuxuat', [XulyPhieuxuat::class, 'xulyphieuxuat'])->name('xulyphieuxuat');
    Route::get('/themphieuxuat', [AdminController::class, 'themphieuxuat'])->name('themphieuxuat');
    Route::get('/suaphieuxuat', [AdminController::class, 'suaphieuxuat'])->name('suaphieuxuat');

    Route::get('/phieunhap', [AdminController::class, 'phieunhap'])->name('phieunhap');
    Route::post('/xulyphieunhap', [XulyPhieunhap::class, 'xulyphieunhap'])->name('xulyphieunhap');
    Route::get('/themphieunhap', [AdminController::class, 'themphieunhap'])->name('themphieunhap');
    Route::get('/suaphieunhap', [AdminController::class, 'suaphieunhap'])->name('suaphieunhap');

    Route::get('/magiamgia', [AdminController::class, 'magiamgia'])->name('magiamgia');
    Route::post('/xulymagiamgia', [XulyMagiamgia::class, 'xulymagiamgia'])->name('xulymagiamgia');

    Route::get('/nguoidung', [AdminController::class, 'nguoidung'])->name('nguoidung');
    Route::post('/xulynguoidung', [XulyNguoidung::class, 'xulynguoidung'])->name('xulynguoidung');

    Route::post('/xulysanpham', [XulySanpham::class, 'xulysanpham'])->name('xulysanpham');

    // Feedback
    Route::get('feedback', [FeedBackController::class, 'index'])->name('feedback.index');
    Route::post('feedback/{id}/reply', [FeedBackController::class, 'reply'])->name('feedback.reply');
});

// Coupon
Route::resource('/coupon', CouponController::class);
Route::patch('admin/coupon/{id}/lock', [\App\Http\Controllers\BackEnd\CouponController::class, 'lock'])->name('coupon.lock');

// ====================== Clear Cache (Debug Tool) ======================
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    return back();
});

// ====================== Test Route ======================
Route::get('/thongtinthanhtoan', function () {
    return dd([
        'nganHang' => 'NCB',
        'soThe' => '9704198526191432198',
        'ngayPhatHanh' => '07/15',
        'tenChuThe' => 'NGUYEN VAN A',
        'matKhauOTP' => '123456'
    ]);
});

// ====================== Email Verification ======================
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->intended('/');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// Route fallback cho 404
Route::fallback(function () {
    return response()->view('404', [], 404);
});
