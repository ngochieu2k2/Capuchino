<?php

namespace App\Http\Controllers\BackEnd;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
class AdminController extends Controller
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
    //tongquan
    //xulysanpham
    public function laptop()
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        $danhSachLaptop = $this->laptop->layDanhSachLaptop();
        $danhSachThuVienHinh = $this->thuVienHinh->layDanhSachThuVienHinh();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        $danhSachQuaTang = $this->quaTang->layDanhSachQuaTang();
        $danhSachHangSanXuatLaptop = []; // loc lai danh sach theo loai hang san xuat laptop can xem
        foreach ($danhSachHangSanXuat as $hangSanXuat) {
            if ($hangSanXuat->cat_mfg == 0) {
                $danhSachHangSanXuatLaptop = array_merge($danhSachHangSanXuatLaptop, [$hangSanXuat]);
            }
        }
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        return view('admin.laptop', compact(
            'danhSachSanPham',
            'danhSachLaptop',
            'danhSachThuVienHinh',
            'danhSachHangSanXuatLaptop',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachQuaTang'
        ));
    }
   //xulylaptop
    public function phukien()
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        $danhSachPhuKien = $this->phuKien->layDanhSachPhuKien();
        $danhSachThuVienHinh = $this->thuVienHinh->layDanhSachThuVienHinh();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        $danhSachQuaTang = $this->quaTang->layDanhSachQuaTang();
        $danhSachHangSanXuatPhuKien = []; // loc lai danh sach theo loai hang san xuat phu kien can xem
        foreach ($danhSachHangSanXuat as $hangSanXuat) {
            if ($hangSanXuat->cat_mfg == 1) {
                $danhSachHangSanXuatPhuKien = array_merge($danhSachHangSanXuatPhuKien, [$hangSanXuat]);
            }
        }
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        return view('admin.phukien', compact(
            'danhSachSanPham',
            'danhSachPhuKien',
            'danhSachThuVienHinh',
            'danhSachHangSanXuatPhuKien',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachQuaTang'
        ));
    }
    //xulyphukien
    public function hangsanxuat()
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        return view('admin.hangsanxuat', compact(
            'danhSachSanPham',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachHangSanXuat'
        ));
    }
    //xulyhangsanxuat
    public function xemphieuxuat(Request $request)
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $rules = [
            'mapx' => 'required|integer|exists:invoice,id_invoice'
        ];
        $messages = [
            'required' => ':attribute bắt buộc nhập',
            'exists' => ':attribute không tồn tại',
            'integer' => ':attribute nhập sai'
        ];
        $attributes = [
            'mapx' => 'Mã phiếu xuất'
        ];
        $request->validate($rules, $messages, $attributes);
        $phieuXuatCanXem = $this->phieuXuat->timPhieuXuatTheoMa($request->mapx);
        $nguoiDungCanXem = $this->nguoiDung->timNguoiDungTheoMa($phieuXuatCanXem->id_users);
        $maGiamGiaCanXem = $this->maGiamGia->timMaGiamGiaTheoMa($phieuXuatCanXem->id_discount);
        $danhSachChiTietPhieuXuatCanXem = $this->chiTietPhieuXuat->timDanhSachChiTietPhieuXuatTheoMaPhieuXuat($phieuXuatCanXem->id_invoice);
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        return view('admin.pdf.phieuxuat', compact(
            'phieuXuatCanXem',
            'nguoiDungCanXem',
            'maGiamGiaCanXem',
            'danhSachChiTietPhieuXuatCanXem',
            'danhSachSanPham'
        ));
    }
    public function suaphieunhap(Request $request)
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $rules = [
            'id' => 'required|integer|exists:purchase_order,id_purchase_order'
        ];
        $messages = [
            'required' => ':attribute bắt buộc nhập',
            'exists' => ':attribute không tồn tại',
            'integer' => ':attribute nhập sai'
        ];
        $attributes = [
            'id' => 'Mã phiếu nhập'
        ];
        $request->validate($rules, $messages, $attributes);
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        $danhSachHangSanXuatLaptop = []; // loc lai danh sach theo loai hang san xuat laptop can xem
        $danhSachHangSanXuatPhuKien = [];
        foreach ($danhSachHangSanXuat as $hangSanXuat) {
            if ($hangSanXuat->cat_mfg == 0) {
                $danhSachHangSanXuatLaptop = array_merge($danhSachHangSanXuatLaptop, [$hangSanXuat]);
            }
            if ($hangSanXuat->cat_mfg == 1) {
                $danhSachHangSanXuatPhuKien = array_merge($danhSachHangSanXuatPhuKien, [$hangSanXuat]);
            }
        }
        $danhSachNguoiDung = $this->nguoiDung->layDanhSachNguoiDung();
        $danhSachNhaCungCap = []; // loc lai danh sach thong tin nha cung cap gom nguoi dung la khach hang hoac doi tac va co trang thai la dang hoat dong
        foreach ($danhSachNguoiDung as $nguoiDung) {
            if (($nguoiDung->roles == 0 || $nguoiDung->roles == 1) && $nguoiDung->status == 1) {
                $danhSachNhaCungCap = array_merge($danhSachNhaCungCap, [$nguoiDung]);
            }
        }
        $phieuNhapCanXem = $this->phieuNhap->timPhieuNhapTheoMa($request->id);
        $nguoiDungCanXem = $this->nguoiDung->timNguoiDungTheoMa($phieuNhapCanXem->id_users);
        $danhSachChiTietPhieuNhapCanXem = $this->chiTietPhieuNhap->timDanhSachChiTietPhieuNhapTheoMaPhieuNhap($request->id);
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        return view('admin.suaphieunhap', compact(
            'phieuNhapCanXem',
            'nguoiDungCanXem',
            'danhSachChiTietPhieuNhapCanXem',
            'danhSachSanPham',
            'danhSachHangSanXuatLaptop',
            'danhSachHangSanXuatPhuKien',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachNhaCungCap'
        ));
    }
    public function suaphieuxuat(Request $request)
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $rules = [
            'id' => 'required|integer|exists:invoice,id_invoice'
        ];
        $messages = [
            'required' => ':attribute bắt buộc nhập',
            'exists' => ':attribute không tồn tại',
            'integer' => ':attribute nhập sai'
        ];
        $attributes = [
            'id' => 'Mã phiếu xuất'
        ];
        $request->validate($rules, $messages, $attributes);
        $danhSachSanPham = $this->sanPham->layDanhSachSanPhamChoPhieu();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        $danhSachHangSanXuatLaptop = []; // loc lai danh sach theo loai hang san xuat laptop can xem
        $danhSachHangSanXuatPhuKien = [];
        foreach ($danhSachHangSanXuat as $hangSanXuat) {
            if ($hangSanXuat->cat_mfg == 0) {
                $danhSachHangSanXuatLaptop = array_merge($danhSachHangSanXuatLaptop, [$hangSanXuat]);
            }
            if ($hangSanXuat->cat_mfg == 1) {
                $danhSachHangSanXuatPhuKien = array_merge($danhSachHangSanXuatPhuKien, [$hangSanXuat]);
            }
        }
        $danhSachNguoiDung = $this->nguoiDung->layDanhSachNguoiDung();
        $danhSachKhachHang = []; // loc lai danh sach thong tin nha cung cap gom nguoi dung la khach hang hoac doi tac va co trang thai la dang hoat dong
        foreach ($danhSachNguoiDung as $nguoiDung) {
            if (($nguoiDung->roles == 0 || $nguoiDung->roles == 1) && $nguoiDung->status == 1) {
                $danhSachKhachHang = array_merge($danhSachKhachHang, [$nguoiDung]);
            }
        }
        $phieuXuatCanXem = $this->phieuXuat->timPhieuXuatTheoMa($request->id);
        $nguoiDungCanXem = $this->nguoiDung->timNguoiDungTheoMa($phieuXuatCanXem->id_users);
        $maGiamGiaCanXem = $this->maGiamGia->timMaGiamGiaTheoMa($phieuXuatCanXem->id_discount);
        $danhSachChiTietPhieuXuatCanXem = $this->chiTietPhieuXuat->timDanhSachChiTietPhieuXuatTheoMaPhieuXuat($request->id);
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        return view('admin.suaphieuxuat', compact(
            'phieuXuatCanXem',
            'nguoiDungCanXem',
            'maGiamGiaCanXem',
            'danhSachChiTietPhieuXuatCanXem',
            'danhSachSanPham',
            'danhSachHangSanXuatLaptop',
            'danhSachHangSanXuatPhuKien',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachKhachHang'
        ));
    }
    public function phieuxuat()
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        
        $danhSachPhieuXuat = $this->phieuXuat->layDanhSachPhieuXuat();       
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        $danhSachNguoiDung = $this->nguoiDung->layDanhSachNguoiDung();
        $danhSachMaGiamGia = $this->maGiamGia->layDanhSachMaGiamGia();
        return view('admin.phieuxuat', compact(
            'danhSachPhieuXuat',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachNguoiDung',
            'danhSachMaGiamGia'
        ));
    }
    public function themphieuxuat(Request $request)
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $danhSachSanPham = $this->sanPham->layDanhSachSanPhamChoPhieu();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        $danhSachHangSanXuatLaptop = []; // loc lai danh sach theo loai hang san xuat laptop can xem
        $danhSachHangSanXuatPhuKien = [];
        foreach ($danhSachHangSanXuat as $hangSanXuat) {
            if ($hangSanXuat->cat_mfg == 0) {
                $danhSachHangSanXuatLaptop = array_merge($danhSachHangSanXuatLaptop, [$hangSanXuat]);
            }
            if ($hangSanXuat->cat_mfg == 1) {
                $danhSachHangSanXuatPhuKien = array_merge($danhSachHangSanXuatPhuKien, [$hangSanXuat]);
            }
        }

        $danhSachNguoiDung = $this->nguoiDung->layDanhSachNguoiDung();
        $danhSachKhachHang = []; // loc lai danh sach thong tin nha cung cap gom nguoi dung la khach hang hoac doi tac va co trang thai dang hoat dong
        foreach ($danhSachNguoiDung as $nguoiDung) {
            if (($nguoiDung->roles == 0 || $nguoiDung->roles == 1) && $nguoiDung->status == 1) {
                $danhSachKhachHang = array_merge($danhSachKhachHang, [$nguoiDung]);
            }
        }
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        return view('admin.themphieuxuat', compact(
            'danhSachSanPham',
            'danhSachHangSanXuatLaptop',
            'danhSachHangSanXuatPhuKien',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachKhachHang'
        ));
    }
    public function phieunhap()
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $danhSachPhieuNhap = $this->phieuNhap->layDanhSachPhieuNhap();
        $danhSachNguoiDung = $this->nguoiDung->layDanhSachNguoiDung();
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        return view('admin.phieunhap', compact(
            'danhSachPhieuNhap',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachNguoiDung'
        ));
    }
    
    public function themphieunhap()
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        $danhSachHangSanXuatLaptop = []; // loc lai danh sach theo loai hang san xuat laptop can xem
        $danhSachHangSanXuatPhuKien = [];
        foreach ($danhSachHangSanXuat as $hangSanXuat) {
            if ($hangSanXuat->cat_mfg == 0) {
                $danhSachHangSanXuatLaptop = array_merge($danhSachHangSanXuatLaptop, [$hangSanXuat]);
            }
            if ($hangSanXuat->cat_mfg == 1) {
                $danhSachHangSanXuatPhuKien = array_merge($danhSachHangSanXuatPhuKien, [$hangSanXuat]);
            }
        }

        $danhSachNguoiDung = $this->nguoiDung->layDanhSachNguoiDung();
        $danhSachNhaCungCap = []; // loc lai danh sach thong tin nha cung cap gom nguoi dung la khach hang hoac doi tac va co trang thai la dang hoat dong
        foreach ($danhSachNguoiDung as $nguoiDung) {
            if (($nguoiDung->roles == 0 || $nguoiDung->roles == 1) && $nguoiDung->status == 1) {
                $danhSachNhaCungCap = array_merge($danhSachNhaCungCap, [$nguoiDung]);
            }
        }
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        return view('admin.themphieunhap', compact(
            'danhSachSanPham',
            'danhSachHangSanXuatLaptop',
            'danhSachHangSanXuatPhuKien',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc',
            'danhSachNhaCungCap'
        ));
    }
    
    public function magiamgia()
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $danhSachMaGiamGia = $this->maGiamGia->layDanhSachMaGiamGia();
        $danhSachPhieuXuat = $this->phieuXuat->layDanhSachPhieuXuat();
        $danhSachPhieuXuatChoXacNhan = $this->phieuXuat->layDanhSachPhieuXuatTheoBoLoc([['invoice.delivery_status', '=', 1]]);
        $danhSachLoiPhanHoiChuaDoc = $this->loiPhanHoi->layDanhSachLoiPhanHoiTheoBoLoc([['feedback.status', '=', 0]]);
        return view('admin.magiamgia', compact(
            'danhSachMaGiamGia',
            'danhSachPhieuXuat',
            'danhSachPhieuXuatChoXacNhan',
            'danhSachLoiPhanHoiChuaDoc'
        ));
    }
    //xulymagiamgia
    public function nguoidung()
    {
        if (!Auth::check() || Auth::user()->roles != 2) {
            return redirect()->route('dangnhap');
        }
        $danhSachNguoiDung = $this->nguoiDung->layDanhSachNguoiDung();
        return view('admin.nguoidung', compact(
            'danhSachNguoiDung'           
        ));
    }
    //xulynguoidung
}
