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

class GioHangController extends Controller
{
    //
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
    public function xulygiohang(Request $request)
    {
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "áp dụng") { // *******************************************************************************************ap dung ma giam gia
            $rules = [
                'maGiamGia' => 'required|string|max:50|min:3|exists:discount,id_discount'
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
                'maGiamGia' => 'Mã giảm giá'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinMaGiamGia = $this->maGiamGia->timMaGiamGiaTheoMa($request->maGiamGia); //tim ma giam gia
            if (strtotime($thongTinMaGiamGia->end_date) - strtotime(date('Y-m-d')) >= 0) { //neu con han su dung
                session(['maGiamGia' => $thongTinMaGiamGia]);
                return back()->with('thongbao', 'Áp dụng mã giảm giá thành công!');
            } else {
                return back()->with('thongbao', 'Mã giảm giá đã hết hạn sử dụng');
            }
            return back()->with('thongbao', 'Áp dụng mã giảm giá thất bại!');
        }
        if ($request->thaoTac == "xóa giỏ hàng") { // *******************************************************************************************xoa gio hang
            $rules = [
                'maSanPhamMuaXoa' => 'required|integer|exists:products,id_products'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'integer' => ':attribute đã nhập sai',
                'exists' => ':attribute không tồn tại'
            ];
            $attributes = [
                'maSanPhamMuaXoa' => 'Mã sản phẩm cần xóa'
            ];
            $request->validate($rules, $messages, $attributes);
            $gioHang = [];
            if (!empty(session('gioHang'))) {
                foreach (session('gioHang') as $ctgh) { // duyet qua gio hang cu
                    if ($request->maSanPhamMuaXoa != $ctgh['id_products']) { // neu chi tiet gio hang khac voi san pham can xoa trong gio hang
                        $gioHang = array_merge($gioHang, [$ctgh]); // thi them chi tiet gio hang do vao gio
                    } // con neu chi tiet gio hang co ma san pham trung voi san pham can xoa trong gio hang thi khong dc them vao gio hang moi
                }
            }
            session(['gioHang' => $gioHang]); //thay gio hang cu bang gio hang moi
            if (empty(session('gioHang'))) {
                session()->forget('maGiamGia');
                session()->forget('gioHang');
            }
            return back()->with('thongbao', 'Xóa sản phẩm SP' . $request->maSanPhamMuaXoa . ' khỏi giỏ hàng thành công!');
        }
        if ($request->thaoTac == "cập nhật") { // *******************************************************************************************sua gio hang
            $rules = [
                'soLuongMuaSua' => 'required|array',
                'soLuongMuaSua.*' => 'required|integer'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'integer' => ':attribute đã nhập sai',
                'array' => ':attribute nhập sai'
            ];
            $attributes = [
                'soLuongMuaSua' => 'Số lượng mua',
                'soLuongMuaSua.*' => 'Số lượng mua'
            ];
            $request->validate($rules, $messages, $attributes);
            $gioHang = [];
            if (!empty(session('gioHang'))) {
                foreach (session('gioHang') as $ctgh) {
                    $soLuongMuaMoi = $request->soLuongMuaSua[$ctgh['id_products']];
                    if ($soLuongMuaMoi > 0) {
                        $ctgh['soluongmua'] = $soLuongMuaMoi;
                        $gioHang = array_merge($gioHang, [$ctgh]); // neu so luong chinh sua gio hang lon hon 0 thi so luong mua trong gio hang thay bang so luong mua moi vua sua
                    }
                }
            }
            session(['gioHang' => $gioHang]);
            if (empty(session('gioHang'))) {
                session()->forget('maGiamGia');
                session()->forget('gioHang');
            }
            return back()->with('thongbao', 'Cập nhật giỏ hàng thành công!');
        }
        if ($request->thaoTac == "thêm giỏ hàng") { // *******************************************************************************************them gio hang
            $rules = [
                'maSanPhamMua' => 'required|integer|exists:products,id_products',
                'soLuongMua' => 'required|integer'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'integer' => ':attribute đã nhập sai'
            ];
            $attributes = [
                'maSanPhamMua' => 'Mã sản phẩm',
                'soLuongMua' => 'Số lượng mua'
            ];
            $request->validate($rules, $messages, $attributes);
            $soLuongMua = $request->soLuongMua;
            $thongTinSanPhamMua = $this->sanPham->timSanPhamTheoMa($request->maSanPhamMua); //tim san pham da them vao gio hang
            if (!empty($thongTinSanPhamMua)) { //neu tim thay
                if (($thongTinSanPhamMua->sale_price <= 0)) { //san pham chua nhap
                    return back()->with('thongbao', 'Liên hệ 090.xxx.xnxx (Mr.Vinh - Quân) Vi Tính Shop để nhận được giá cụ thể nhất!');
                }
            }
            $thongTinHinh = $this->thuVienHinh->timThuVienHinhTheoMa($thongTinSanPhamMua->id_photo); //tim hinh san pham da them vao gio hang
            $thongTinQuaTang = $this->quaTang->timQuaTangTheoMa($thongTinSanPhamMua->id_gift); //tim qua tang cua san pham da them vao gio hang
            // $thongTinHangSanXuat=$this->hangSanXuat->timHangSanXuatTheoMa($thongTinSanPhamMua->id_mfg);
            $danhSachSanPhamTang = [];
            $flag = false;
            foreach ($thongTinQuaTang as $giaTri) {
                if ($flag && !empty($giaTri)) {
                    $sanPhamTang = $this->sanPham->timSanPhamTheoMa($giaTri);
                    if (!empty($sanPhamTang)) {    // Kiểm tra nếu sản phẩm quà tặng tìm thấy không rỗng.
                        $danhSachSanPhamTang = array_merge($danhSachSanPhamTang, [$sanPhamTang]);  // them qua tang
                    }
                }
                if (is_string($giaTri)) $flag = true;
            }
            if (!empty($thongTinSanPhamMua) && !empty($thongTinHinh)) {
                $chiTietGioHang = [
                    'id_products' => $thongTinSanPhamMua->id_products,
                    // 'name_mfg' => $thongTinHangSanXuat->name_mfg,
                    'name_products' => $thongTinSanPhamMua->name_products,
                    'guarantee' => $thongTinSanPhamMua->guarantee,
                    'sale_price' => $thongTinSanPhamMua->sale_price,
                    'promotional_price' => $thongTinSanPhamMua->promotional_price,
                    'photo' => $thongTinHinh->photo_1,
                    'gift' => $danhSachSanPhamTang,
                    'soluongmua' => $soLuongMua
                ];
                $gioHang = [];
                $flag = false;
                if (!empty(session('gioHang'))) {
                    foreach (session('gioHang') as $ctgh) {
                        if ($ctgh['id_products'] == $chiTietGioHang['id_products']) { // tim xem chi tiet gio hang vua them co san trong gio hang chua
                            $ctgh['soluongmua'] += $chiTietGioHang['soluongmua']; // neu co thi tang so luong mua
                            $flag = true; // chi tiet gio hang nay da dc them vao gio hang bien co se duoc bat len de khoi phai them vao lan nua
                        }
                        $gioHang = array_merge($gioHang, [$ctgh]);
                    }
                }
                if (!$flag) { // bien co chua bat thi la san pham nay chua co trong gio hang va them vao thanh chi tiet gio hang moi
                    $gioHang = array_merge($gioHang, [$chiTietGioHang]);
                }
                session(['gioHang' => $gioHang]);
                return back()->with('thongbao', 'Thêm giỏ hàng thành công!');
            }
        }
        return redirect()->route('/')->with('thongbao', 'Thao tác thất bại vui lòng thử lại!');
    }
}
