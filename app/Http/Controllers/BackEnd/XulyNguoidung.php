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

class XulyNguoidung extends Controller
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
    public function xulynguoidung(Request $request)
    {
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "thêm người dùng") { // *******************************************************************************************them nguoi dung
            $rules = [
                'hoTen' => 'required|string|max:50|min:3',
                'soDienThoai' => 'required|numeric|digits:10|unique:users,phone',
                'diaChi' => 'required|string|max:255|min:3',
                'loaiNguoiDung' => 'required|integer|between:0,2'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute đã nhập sai',
                'integer' => ':attribute đã nhập sai',
                'numeric' => ':attribute đã nhập sai',
                'unique' => ':attribute đã tồn tại',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'between' => ':attribute vượt quá số lượng cho phép',
                'digits' => ':attribute không đúng :digits ký tự'
            ];
            $attributes = [
                'hoTen' => 'Họ tên',
                'soDienThoai' => 'Số điện thoại',
                'diaChi' => 'Địa chỉ',
                'loaiNguoiDung' => 'Loại người dùng'
            ];
            $request->validate($rules, $messages, $attributes);
            $ngayTao = date("Y-m-d H:i:s");
            $dataNguoiDung = [
                NULL, //manguoidung tu tang
                $request->hoTen,
                $request->soDienThoai,
                $request->diaChi,
                1, //trangthai 0 la bi khoa, 1 la dang hoat dong
                $request->loaiNguoiDung, //loainguoidung 0 là khách hàng, 1 là đối tác, 2 là nhân viên
                NULL, //email
                NULL, //matkhau
                $ngayTao
            ];
            if ($request->loaiNguoiDung == 2) {
                $rules = [
                    'email' => 'required|email|max:150|min:5|unique:users,email',
                    'matKhau' => 'required|string|max:32|min:8',
                    'nhapLaiMatKhau' => 'required|string|max:32|min:8|same:matKhau'
                ];
                $messages = [
                    'required' => ':attribute bắt buộc nhập',
                    'unique' => ':attribute đã tồn tại',
                    'string' => ':attribute đã nhập sai',
                    'email' => ':attribute không đúng định dạng email',
                    'min' => ':attribute tối thiểu :min ký tự',
                    'max' => ':attribute tối đa :max ký tự',
                    'same' => ':attribute không khớp với mật khẩu'
                ];
                $attributes = [
                    'email' => 'Email',
                    'matKhau' => 'Mật khẩu',
                    'nhapLaiMatKhau' => 'Nhập lại mật khẩu',
                ];
                $request->validate($rules, $messages, $attributes);
                $dataNguoiDung = [
                    NULL, //manguoidung tu tang
                    $request->hoTen,
                    $request->soDienThoai,
                    $request->diaChi,
                    1, //trangthai 0 la bi khoa, 1 la dang hoat dong
                    $request->loaiNguoiDung, //loainguoidung 0 là khách hàng, 1 là đối tác, 2 là nhân viên
                    $request->email,
                    bcrypt($request->matKhau),
                    $ngayTao
                ];
            }
            $this->nguoiDung->themNguoiDung($dataNguoiDung); //them nguoi dung vao database
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Thêm người dùng thành công'
            )->with(
                'loaithongbao',
                'success'
            );
        }
        if ($request->thaoTac == "đổi trạng thái người dùng") { // *******************************************************************************************doi trang thai nguoi dung
            $rules = [
                'maNguoiDungKhoa' => 'required|integer|exists:users,id_users',
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'integer' => ':attribute nhập sai'
            ];
            $attributes = [
                'maNguoiDungKhoa' => 'Mã người dùng'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoMa($request->maNguoiDungKhoa); // tim người dùng
            // ***********Xu ly khoa nguoi dung
            if ($thongTinNguoiDung->status == 0) { // so sánh trạng thái 0: bị khóa || 1: hoạt động
                $thongTinNguoiDung->status = 1;
            } else if ($thongTinNguoiDung->status == 1) {
                $danhSachPhieuXuat = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.id_users', '=', $thongTinNguoiDung->id_users]]);
                foreach ($danhSachPhieuXuat as $px) {
                    if ($px->delivery_status > 0 && $px->delivery_status < 4) { // 1 la cho xac nhan //2 la dang chuan bi hang //3 la dang giao hang
                        $dataPhieuXuat = [
                            0 //chuyen het lai thanh da huy
                        ];
                        $this->phieuXuat->doiTinhTrangGiaoHangPhieuXuat($dataPhieuXuat, $px->id_invoice); //doi tinh trang giao hang phieu xuat tren database
                    }
                }
                $thongTinNguoiDung->status = 0;
            }
            $dataNguoiDung = [
                $thongTinNguoiDung->status
            ];
            $this->nguoiDung->doiTrangThaiNguoiDung($dataNguoiDung, $thongTinNguoiDung->id_users);
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Đổi trạng thái người dùng thành công'
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
