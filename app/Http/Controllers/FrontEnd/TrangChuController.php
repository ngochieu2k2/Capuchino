<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Models\ThuVienHinh;
use App\Models\Laptop;
use App\Models\PhuKien;
use App\Models\HangSanXuat;
use App\Models\QuaTang;
use App\Models\NguoiDung;
use App\Models\PhieuXuat;
use App\Models\ChiTietPhieuXuat;
use App\Models\MaGiamGia;
use App\Models\LoiPhanHoi;
use Illuminate\Http\Request;

class TrangChuController extends Controller
{
    private $sanPham;
    private $laptop;
    private $phuKien;
    private $thuVienHinh;
    private $hangSanXuat;
    private $quaTang;
    private $nguoiDung;
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
        $this->phieuXuat = new PhieuXuat();
        $this->chiTietPhieuXuat = new ChiTietPhieuXuat();
        $this->maGiamGia = new MaGiamGia();
        $this->loiPhanHoi = new LoiPhanHoi();
    }
    public function trangchu()
    {
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        $danhSachSanPhamMoiRaMat = $this->sanPham->layDanhSachSanPhamTheoBoLoc([], NULL, 'moinhat');
        $danhSachSanPhamBanChay = $this->sanPham->layDanhSachSanPhamTheoBoLoc([], NULL, 'banchaynhat');
        $danhSachSanPhamUuDai = $this->sanPham->layDanhSachSanPhamTheoBoLoc([], NULL, 'uudainhat');
        $danhSachThuVienHinh = $this->thuVienHinh->layDanhSachThuVienHinh();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        $danhSachLaptop = $this->laptop->layDanhSachLaptop();
        $danhSachSanPhamLaLaptop = [];
        $danhSachSanPhamLaPhuKien = [];
        $danhSachLaptopSinhVien = [];
        $danhSachLaptopDoHoa = [];
        $danhSachLaptopGaming = [];
        foreach ($danhSachSanPham as $sanpham) {
            if ($sanpham->cat_products == 1) { // la phu kien
                $danhSachSanPhamLaPhuKien = array_merge($danhSachSanPhamLaPhuKien, [$sanpham]);
            }
            if ($sanpham->cat_products == 0) { // la laptop
                $danhSachSanPhamLaLaptop = array_merge($danhSachSanPhamLaLaptop, [$sanpham]);
                $thongTinLaptop = $this->laptop->timLaptopTheoMa($sanpham->id_laptop);
                if ($thongTinLaptop->demand == 'Sinh Viên') { // la laptop nhu cau la sinh vien
                    $danhSachLaptopSinhVien = array_merge($danhSachLaptopSinhVien, [$sanpham]);
                } elseif ($thongTinLaptop->demand == 'Đồ Họa') { // la laptop nhu cau la do hoa
                    $danhSachLaptopDoHoa = array_merge($danhSachLaptopDoHoa, [$sanpham]);
                } elseif ($thongTinLaptop->demand == 'Gaming') { // la laptop nhu cau la gaming
                    $danhSachLaptopGaming = array_merge($danhSachLaptopGaming, [$sanpham]);
                }
            }
        }
        return view('user.trangchu', compact(
            'danhSachSanPham',
            'danhSachSanPhamMoiRaMat',
            'danhSachSanPhamBanChay',
            'danhSachSanPhamUuDai',
            'danhSachThuVienHinh',
            'danhSachHangSanXuat',
            'danhSachLaptop',
            'danhSachLaptopSinhVien',
            'danhSachLaptopDoHoa',
            'danhSachLaptopGaming',
            'danhSachSanPhamLaLaptop',
            'danhSachSanPhamLaPhuKien'
        ));
    }
    public function giohang()
    {
        if (empty(session('gioHang'))) return redirect()->route('/')->with('thongbao', 'Giỏ hàng chưa có sản phẩm!');
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        return view('user.giohang', compact(
            'danhSachHangSanXuat'
        ));
    }
    public function yeuthich()
    {
        if (empty(session('yeuThich'))) return redirect()->route('/')->with('thongbao', 'Danh sách yêu thích chưa có sản phẩm!');
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        return view('user.yeuthich', compact(
            'danhSachHangSanXuat',

        ));
    }
    public function baohanh()
    {
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        return view('user.baohanh', compact(
            'danhSachHangSanXuat'
        ));
    }
    public function tragop(Request $request)
    {
        session()->flush();
        return back();
    }
    public function lienhe()
    {
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        return view('user.lienhe', compact(
            'danhSachHangSanXuat'
        ));
    }
    public function xulylienhe(Request $request)
    {
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "gửi lời nhắn") { // *******************************************************************************************gui loi nhan
            $rules = [
                'hoTen' => 'required|string|max:50|min:3',
                'soDienThoai' => 'required|numeric|digits:10',
                'diaChi' => 'required|string|max:255|min:3',
                'noiDung' => 'required|string|max:255|min:3'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute đã nhập sai',
                'numeric' => ':attribute đã nhập sai',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'digits' => ':attribute không đúng :digits ký tự'
            ];
            $attributes = [
                'hoTen' => 'Họ tên',
                'soDienThoai' => 'Số điện thoại',
                'diaChi' => 'Địa chỉ',
                'noiDung' => 'Nội dung'
            ];
            $request->validate($rules, $messages, $attributes);
            $ngayTao = date("Y-m-d H:i:s");
            $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoSoDienThoai($request->soDienThoai); //tim nguoi dung da ton tai hay chua
            if (!empty($thongTinNguoiDung)) { //neu tim thay
                if ($thongTinNguoiDung->status == 0) { //neu nguoi dung dang bi khoa
                    return back()->with('thongbao', 'Thông tin người dùng hiện đang bị tạm khóa do hủy quá nhiều đơn!');
                }
                $dataNguoiDung = [
                    $request->hoTen,
                    $thongTinNguoiDung->phone,
                    $request->diaChi,
                    $thongTinNguoiDung->roles, //loainguoidung 0 là khách hàng, 1 là đối tác, 2 là nhân viên
                    $thongTinNguoiDung->email,
                    $thongTinNguoiDung->password
                ];
                $this->nguoiDung->suaNguoiDung($dataNguoiDung, $thongTinNguoiDung->id_users); //sua lai thong tin nguoi dung
            } else {
                $dataNguoiDung = [
                    NULL, //manguoidung tu tang
                    $request->hoTen,
                    $request->soDienThoai,
                    $request->diaChi,
                    1, //trangthai 0 la bi khoa, 1 la dang hoat dong
                    0, //loainguoidung 0 là khách hàng, 1 là đối tác, 2 là nhân viên
                    NULL, //email
                    NULL, //matkhau
                    $ngayTao
                ];
                $this->nguoiDung->themNguoiDung($dataNguoiDung); //them nguoi dung vao database
                $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoNgayTao($ngayTao); //tim nguoi dung vua them
            }
            $dataLoiPhanHoi = [
                $request->noiDung, //noidung,
                0, //trangthai, 0 la chua doc // 1 la da doc
                $thongTinNguoiDung->id_users, //manguoidung,
                $ngayTao //ngaytao
            ];
            $this->loiPhanHoi->themLoiPhanHoi($dataLoiPhanHoi); //them loi phan hoi vao database
            return redirect()->route('/')->with('thongbao', 'Gửi lời nhắn thành công, sẽ có nhân viên liên hệ bạn sớm nhất có thể!');
        }
        return redirect()->route('/')->with('thongbao', 'Thao tác thất bại vui lòng thử lại!');
    }
}
