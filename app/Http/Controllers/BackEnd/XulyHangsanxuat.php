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

class XulyHangsanxuat extends Controller
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
    public function xulyhangsanxuat(Request $request)
    {
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "thêm hãng sản xuất") { // *******************************************************************************************them hang san xuat
            $rules = [
                'tenHang' => 'required|string|max:50|min:1',
                'loaiHang' => 'required|integer|between:0,1'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute nhập sai',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'between' => ':attribute vượt quá số lượng cho phép',
                'integer' => ':attribute nhập sai',
                'array' => ':attribute nhập sai'
            ];
            $attributes = [
                'tenHang' => 'Tên hãng',
                'loaiHang' => 'Loại hãng'
            ];
            $request->validate($rules, $messages, $attributes);
            $tenHang = mb_strtoupper($request->tenHang, 'UTF-8');
            $thongTinHang = $this->hangSanXuat->timHangSanXuatTheoTen($tenHang);
            if (!empty($thongTinHang)) {
                if ($thongTinHang->cat_mfg == $request->loaiHang) {
                    return back()->with(
                        'tieudethongbao',
                        'Thao tác thất bại'
                    )->with(
                        'thongbao',
                        'Tên hãng sản xuất đã tồn tại, vui lòng nhập lại!'
                    )->with(
                        'loaithongbao',
                        'danger'
                    );
                }
            }
            $dataHangSanXuat = [
                NULL, //mahang tu dong
                $tenHang,
                $request->loaiHang
            ];
            $this->hangSanXuat->themHangSanXuat($dataHangSanXuat); //them hang san xuat vao database
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Thêm hãng sản xuất thành công'
            )->with(
                'loaithongbao',
                'success'
            );
        }
        if ($request->thaoTac == "xóa hãng sản xuất") { // *******************************************************************************************xoa hang san xuat
            $rules = [
                'maHangXoa' => 'required|integer|exists:manufacturer,id_mfg|unique:products,id_mfg'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'unique' => 'Tồn tại sản phẩm thuộc :attribute này nên không thể xóa',
                'integer' => ':attribute đã nhập sai'
            ];
            $attributes = [
                'maHangXoa' => 'Hãng sản xuất'
            ];
            $request->validate($rules, $messages, $attributes);
            $this->hangSanXuat->xoaHangSanXuat($request->maHangXoa); //xoa hang san xuat tren database
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Thêm hãng sản xuất thành công'
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
