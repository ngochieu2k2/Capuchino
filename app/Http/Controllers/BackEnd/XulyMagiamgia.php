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

class XulyMagiamgia extends Controller
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
    public function xulymagiamgia(Request $request)
    {
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "sửa mã giảm giá") { // *******************************************************************************************sua ma giam gia
            $rules = [
                'maGiamGiaSua' => 'required|string|max:50|min:3|exists:discount,id_discount',
                'ngayBatDauSua' => 'required|date_format:Y-m-d',
                'ngayKetThucSua' => 'required|date_format:Y-m-d|after_or_equal:' . $request->ngayBatDauSua,
                'moTaSua' => 'max:255'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute đã nhập sai',
                'exists' => ':attribute không tồn tại',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'date_format' => ':attribute không đúng định dạng ngày/tháng/năm',
                'ngayKetThucSua.after_or_equal' => 'Ngày kết thúc phải sau ' . date("d/m/Y", strtotime($request->ngayBatDauSua))
            ];
            $attributes = [
                'maGiamGiaSua' => 'Mã giảm giá',
                'ngayBatDauSua' => 'Ngày bắt đầu',
                'ngayKetThucSua' => 'Ngày kết thúc',
                'moTaSua' => 'Mô tả'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinMaGiamGia = $this->maGiamGia->timMaGiamGiaTheoMa($request->maGiamGiaSua); //tim ma giam gia
            if ($thongTinMaGiamGia->describes != $request->moTaSua) { //so sanh mo ta
                $thongTinMaGiamGia->describes = $request->moTaSua;
            }
            if ($thongTinMaGiamGia->start_date != $request->ngayBatDauSua) { //so sanh ngay bat dau
                $thongTinMaGiamGia->start_date = $request->ngayBatDauSua;
            }
            if ($thongTinMaGiamGia->end_date != $request->ngayKetThucSua) { //so sanh ngay ket thuc
                $thongTinMaGiamGia->end_date = $request->ngayKetThucSua;
            }
            $dataMaGiamGia = [
                $thongTinMaGiamGia->describes,
                $thongTinMaGiamGia->start_date,
                $thongTinMaGiamGia->end_date
            ];
            if (isset($request->hetHanCheck)) {
                if ($request->hetHanCheck == "on") {
                    $dataMaGiamGia = [
                        $thongTinMaGiamGia->describes,
                        date("Y-m-d", strtotime('-2 days')), //hom kia
                        date("Y-m-d", strtotime('-1 days')) //hom qua
                    ];
                } else {
                    return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thời gian áp dụng mã nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                }
            }
            $this->maGiamGia->suaMaGiamGia($dataMaGiamGia, $thongTinMaGiamGia->id_discount); //sua ma giam gia tren database
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Sửa mã giảm giá thành công'
            )->with(
                'loaithongbao',
                'success'
            );
        }
        if ($request->thaoTac == "xóa mã giảm giá") { // *******************************************************************************************xoa ma giam gia
            $rules = [
                'maGiamGiaXoa' => 'required|string|max:50|min:3|exists:discount,id_discount|unique:invoice,id_discount'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'unique' => ':attribute đã được áp dụng cho phiếu xuất',
                'string' => ':attribute đã nhập sai'
            ];
            $attributes = [
                'maGiamGiaXoa' => 'Mã giảm giá'
            ];
            $request->validate($rules, $messages, $attributes);
            $this->maGiamGia->xoaMaGiamGia($request->maGiamGiaXoa); //xoa ma giam gia tren database
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Thêm mã giảm giá thành công'
            )->with(
                'loaithongbao',
                'success'
            );
        }
        if ($request->thaoTac == "thêm mã giảm giá") { // *******************************************************************************************them ma giam gia
            $rules = [
                'maGiamGia' => 'required|string|max:50|min:3|unique:discount,id_discount',
                'soTienGiam' => 'required|string|max:255|min:1',
                'ngayBatDau' => 'required|date_format:Y-m-d|after_or_equal:' . date("Y-m-d"),
                'ngayKetThuc' => 'required|date_format:Y-m-d|after_or_equal:' . $request->ngayBatDau,
                'moTa' => 'max:255'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute đã nhập sai',
                'unique' => ':attribute đã tồn tại',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'date_format' => ':attribute không đúng định dạng ngày/tháng/năm',
                'ngayBatDau.after_or_equal' => 'Ngày bắt đầu phải sau ' . date("d/m/Y"),
                'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau ' . date("d/m/Y", strtotime($request->ngayBatDau))
            ];
            $attributes = [
                'maGiamGia' => 'Mã giảm giá',
                'soTienGiam' => 'Số tiền giảm',
                'ngayBatDau' => 'Ngày bắt đầu',
                'ngayKetThuc' => 'Ngày kết thúc',
                'moTa' => 'Mô tả'
            ];
            $request->validate($rules, $messages, $attributes);
            $soTienGiam = explode(',', $request->soTienGiam);
            $temp = "";
            foreach ($soTienGiam as $stg) {
                $temp = $temp . $stg;
            }
            $soTienGiam = $temp;
            if (!is_numeric($soTienGiam)) { // so tien giam nhap vao sai dinh dang, quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền giảm nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $dataMaGiamGia = [
                $request->maGiamGia, //magiamgia
                $request->moTa,
                $soTienGiam,
                $request->ngayBatDau,
                $request->ngayKetThuc
            ];
            $this->maGiamGia->themMaGiamGia($dataMaGiamGia); //them ma giam gia vao database
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Thêm mã giảm giá thành công'
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
