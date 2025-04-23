<?php

namespace App\Http\Controllers\BackEnd;
use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Models\ThuVienHinh;
use App\Models\Laptop;
use App\Models\PhuKien;
use App\Models\HangSanXuat;
use App\Models\QuaTang;
use App\Models\NguoiDung;
use App\Models\PhieuNhap;
use App\Models\ChiTietPhieuNhap;
use App\Models\PhieuXuat;
use App\Models\ChiTietPhieuXuat;
use App\Models\MaGiamGia;
use App\Models\LoiPhanHoi;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use PDF;
use Illuminate\Http\Request;

class XulySanpham extends Controller
{
    private $sanPham;
    private $laptop;
    private $phuKien;
    private $thuVienHinh;
    private $hangSanXuat;
    private $quaTang;
    private $nguoiDung;
    private $phieuNhap;
    private $chiTietPhieuNhap;
    private $phieuXuat;
    private $chiTietPhieuXuat;
    private $maGiamGia;
    private $loiPhanHoi;

    public function __construct()
    {
        $this->sanPham = new SanPham();
        $this->laptop = new Laptop();
        $this->phuKien = new PhuKien();
        $this->thuVienHinh = new ThuVienHinh();
        $this->hangSanXuat = new HangSanXuat();
        $this->quaTang = new QuaTang();
        $this->nguoiDung = new NguoiDung();
        $this->phieuNhap = new PhieuNhap();
        $this->chiTietPhieuNhap = new ChiTietPhieuNhap();
        $this->phieuXuat = new PhieuXuat();
        $this->chiTietPhieuXuat = new ChiTietPhieuXuat();
        $this->maGiamGia = new MaGiamGia();
        $this->loiPhanHoi = new LoiPhanHoi();
    }
    public function xulysanpham(Request $request)
    {
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "cập nhật giá") { // *******************************************************************************************cap nhat gia san pham
            $rules = [
                'maSanPhamSuaGia' => 'required|integer|exists:products,id_products',
                'giaBan' => 'required|string|max:255|min:1'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'integer' => ':attribute nhập sai',
                'string' => ':attribute nhập sai',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự'
            ];
            $attributes = [
                'maSanPhamSuaGia' => 'Mã sản phẩm',
                'giaBan' => 'Giá bán'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($request->maSanPhamSuaGia); //tim san pham
            $giaBan = explode(',', $request->giaBan);
            $temp = "";
            foreach ($giaBan as $gb) {
                $temp = $temp . $gb;
            }
            $giaBan = $temp;
            if (!is_numeric($giaBan) || $giaBan <= $thongTinSanPham->entry_price || $giaBan <= 0) { // gia ban nhap vao khong phai ky tu so hoac thap hon gia nhap, quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Giá bán nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $dataSanPham = [
                $giaBan,
                NULL //giakhuyenmai
            ];
            if (isset($request->giaKhuyenMaiCheck)) {
                if ($request->giaKhuyenMaiCheck == "on") {
                    $rules = [
                        'giaKhuyenMai' => 'required|string|max:255|min:1'
                    ];
                    $messages = [
                        'required' => ':attribute bắt buộc nhập',
                        'string' => ':attribute nhập sai',
                        'min' => ':attribute tối thiểu :min ký tự',
                        'max' => ':attribute tối đa :max ký tự'
                    ];
                    $attributes = [
                        'giaKhuyenMai' => 'Giá khuyến mãi'
                    ];
                    $request->validate($rules, $messages, $attributes);
                    $giaKhuyenMai = explode(',', $request->giaKhuyenMai);
                    $temp = "";
                    foreach ($giaKhuyenMai as $gkm) {
                        $temp = $temp . $gkm;
                    }
                    $giaKhuyenMai = $temp;
                    if (!is_numeric($giaKhuyenMai) || $giaKhuyenMai >= $giaBan || $giaKhuyenMai <= 0) { // gia khuyen mai nhap vao khong phai ky tu so hoac thap hon gia nhap hoac lon hon gia ban, quay lai trang truoc va bao loi
                        return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Giá khuyến mãi nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                    }
                    $dataSanPham = [
                        $giaBan,
                        $giaKhuyenMai //giakhuyenmai
                    ];
                } else {
                    return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Giá khuyến mãi nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                }
            }
            $this->sanPham->capNhatGia($dataSanPham, $thongTinSanPham->id_products); //cap nhat gia san pham tren database
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Cập nhật giá sản phẩm thành công'
            )->with(
                'loaithongbao',
                'success'
            );
        }
        return back()->with(
            'tieudethongbao',
            'Thao tác thất bại'
        )->with(
            'thongbao',
            'Vui lòng thử lại!'
        )->with(
            'loaithongbao',
            'danger'
        );
    }
}
