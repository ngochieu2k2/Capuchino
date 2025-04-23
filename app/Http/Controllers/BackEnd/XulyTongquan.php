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

class XulyTongquan extends Controller
{
    //
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
    public function tongquan(Request $request)
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        $soLuongLaptop = 0;
        $soLuongPhuKien = 0;
        foreach ($danhSachSanPham as $sanpham) {
            if ($sanpham->cat_products == 0) { // la laptop
                $soLuongLaptop += $sanpham->qty;
            }
            if ($sanpham->cat_products == 1) { // la phu kien
                $soLuongPhuKien += $sanpham->qty;
            }
        }
        $soLuongDonHang = count($this->phieuXuat->layDanhSachPhieuXuat());
        $soLuongNguoiDung = count($this->nguoiDung->layDanhSachNguoiDung());
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        $danhSachLoiPhanHoi = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc(NULL);
        $doanhThuTuanNay = $this->phieuXuat->doanhThuTuanNay();
        if (isset($request->thaotac)) {
            if ($request->thaotac == "doitrangthai") { // *******************************************************************************************doi trang thai loi phan hoi// loi nhan lien he
                $rules = [
                    'id_feedback' => 'required|integer|exists:feedback,id_feedback'
                ];
                $messages = [
                    'required' => ':attribute bắt buộc nhập',
                    'exists' => ':attribute không tồn tại',
                    'integer' => ':attribute nhập sai'
                ];
                $attributes = [
                    'id_feedback' => 'Mã lời phản hồi'
                ];
                $request->validate($rules, $messages, $attributes);
                $thongTinLoiPhanHoi = $this->loiPhanHoi->timLoiPhanHoiTheoMa($request->id_feedback);
                if ($thongTinLoiPhanHoi->status == 0) {
                    $thongTinLoiPhanHoi->status = 1;
                } elseif ($thongTinLoiPhanHoi->status == 1) {
                    $thongTinLoiPhanHoi->status = 0;
                }
                $dataLoiPhanHoi = [
                    $thongTinLoiPhanHoi->status
                ];
                $this->loiPhanHoi->doiTrangThaiLoiPhanHoi($dataLoiPhanHoi, $thongTinLoiPhanHoi->id_feedback);
                return redirect('tongquan#loiphanhoi');
            } elseif ($request->thaotac == "doitrangthaitatca" && !empty($danhSachLoiPhanHoi)) {
                $this->loiPhanHoi->doiTrangThaiLoiPhanHoiTatCa();
                return redirect('tongquan#loiphanhoi');
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
        return view('admin.tongquan', compact(
            'soLuongLaptop',
            'soLuongPhuKien',
            'soLuongDonHang',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachLoiPhanHoi',
            'doanhThuTuanNay',
            'soLuongNguoiDung'
        ));
    }
}
