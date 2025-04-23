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

class XulyPhieunhap extends Controller
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
    public function xulyphieunhap(Request $request)
    {
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "xóa phiếu nhập") { // *******************************************************************************************xoa phieu nhap
            $rules = [
                'maPhieuNhapXoa' => 'required|integer|exists:purchase_order,id_purchase_order'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'integer' => ':attribute đã nhập sai'
            ];
            $attributes = [
                'maPhieuNhapXoa' => 'Mã phiếu nhập'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinPhieuNhap = $this->phieuNhap->timPhieuNhapTheoMa($request->maPhieuNhapXoa); //tim phieu nhap can xoa
            if (!empty($thongTinPhieuNhap)) {
                $danhSachChiTietPhieuNhap = $this->chiTietPhieuNhap->timDanhSachChiTietPhieuNhapTheoMaPhieuNhap($thongTinPhieuNhap->id_purchase_order); //tim chi tiet phieu nhap can xoa
                if (!empty($danhSachChiTietPhieuNhap)) {
                    foreach ($danhSachChiTietPhieuNhap as $ctpn) {
                        $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($ctpn->id_products); //tim san pham can chinh so luong
                        if (!empty($thongTinSanPham)) {
                            $dataSanPham = [
                                $thongTinSanPham->qty - $ctpn->qty
                            ];
                            $this->sanPham->suaSoLuong($dataSanPham, $thongTinSanPham->id_products); //chinh so luong ton kho san pham tren database
                            $this->chiTietPhieuNhap->xoaChiTietPhieuNhap($ctpn->id_purchase_order_details); //xoa chi tiet phieu nhap tren database
                        }
                    }
                }
                $this->phieuNhap->xoaPhieuNhap($thongTinPhieuNhap->id_purchase_order); //xoa phieu nhap tren database
                return back()->with(
                    'tieudethongbao',
                    'Thao tác thành công'
                )->with(
                    'thongbao',
                    'Xóa phiếu nhập thành công'
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
                'Xóa phiếu nhập thất bại'
            )->with(
                'loaithongbao',
                'danger'
            );
        }
        if ($request->thaoTac == "sửa phiếu nhập") { // *******************************************************************************************sua phieu nhap
            $rules = [
                'maPhieuNhapSua' => 'required|integer|exists:purchase_order,id_purchase_order',
                'chiTietPhieuNhap' => 'array',
                'chiTietPhieuNhap.*' => 'required|string|max:255|min:3',
                'soLuong' => 'array',
                'soLuong.*' => 'required|integer',
                'donGia' => 'array',
                'donGia.*' => 'required|string|max:255|min:1',
                'thongTinNguoiDung' => 'required|string|max:255|min:3',
                'ghiChu' => 'max:255',
                'tongTien' => 'required|numeric',
                'daThanhToan' => 'required|string|max:255|min:1'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute nhập sai',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối thiểu :max ký tự',
                'integer' => ':attribute nhập sai',
                'numeric' => ':attribute nhập sai',
                'array' => ':attribute nhập sai'
            ];
            $attributes = [
                'chiTietPhieuNhap' => 'Chi tiết phiếu nhập',
                'chiTietPhieuNhap.*' => 'Chi tiết phiếu nhập *',
                'soLuong' => 'Số lượng',
                'soLuong.*' => 'Số lượng *',
                'donGia' => 'Đơn giá',
                'donGia.*' => 'Đơn giá *',
                'thongTinNguoiDung' => 'Thông tin người dùng',
                'ghiChu' => 'Ghi chú',
                'tongTien' => 'Tổng tiền',
                'daThanhToan' => 'Đã thanh toán'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinPhieuNhap = $this->phieuNhap->timPhieuNhapTheoMa($request->maPhieuNhapSua); //tim phieu nhap
            // ***********Xu ly phieu nhap
            $thongTinNguoiDung = explode(' | ', $request->thongTinNguoiDung);
            if (empty($thongTinNguoiDung[0]) || empty($thongTinNguoiDung[1]) || empty($thongTinNguoiDung[2])) { // thong tin nguoi dung nhap vao sai cu phap quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa nhà cung cấp, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $maNguoiDung = explode('ND', $thongTinNguoiDung[0]);
            $maNguoiDung = $maNguoiDung[1];
            $hoTen = $thongTinNguoiDung[1];
            $soDienThoai = $thongTinNguoiDung[2];

            if (!is_numeric($maNguoiDung)) { // ma nguoi dung nhap vao khong phai ky tu so quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa nhà cung cấp, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            if ($thongTinPhieuNhap->id_users  != $maNguoiDung) { // thong tin nguoi dung khong khop va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa nhà cung cấp, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoMa($maNguoiDung);
            if (empty($thongTinNguoiDung)) { // khong tim thay nguoi dung tren database quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa nhà cung cấp, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            if ($hoTen != $thongTinNguoiDung->name_users || $soDienThoai != $thongTinNguoiDung->phone) { // thong tin nguoi dung khong khop va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa nhà cung cấp, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            if ($thongTinNguoiDung->status == 0) { // thong tin nguoi dung dang bi khoa
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Trạng thái người dùng đang bị khóa, không thể thao tác!')->with('loaithongbao', 'danger');
            }
            $soTienDaThanhToan = explode(',', $request->daThanhToan);
            $temp = "";
            foreach ($soTienDaThanhToan as $stdtt) {
                $temp = $temp . $stdtt;
            }
            $soTienDaThanhToan = $temp;
            if (!is_numeric($soTienDaThanhToan)) { // so tien da thanh toan nhap vao khong phai ky tu so quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền đã thanh toán nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $ghiChu = $thongTinPhieuNhap->note;
            $tongTien = $thongTinPhieuNhap->total_money;
            $congNo = $thongTinPhieuNhap->debt;
            $congNoSua = $soTienDaThanhToan - $request->tongTien;
            if ($request->ghiChu != $ghiChu) { //ghi chu vua chinh sua khac voi ghi chu cu
                $ghiChu = $request->ghiChu;
            }
            if ($request->tongTien != $tongTien) { //tong tien vua chinh sua khac voi tong tien cu
                $tongTien = $request->tongTien;
            }
            if ($congNoSua != $congNo) { //ghi chu vua chinh sua khac voi ghi chu cu
                $congNo = $congNoSua;
            }
            $dataPhieuNhap = [
                $ghiChu,
                $tongTien,
                $congNo
            ];
            $this->phieuNhap->suaPhieuNhap($dataPhieuNhap, $thongTinPhieuNhap->id_purchase_order); //sua phieu nhap tren database
            $danhSachChiTietPhieuNhap = $this->chiTietPhieuNhap->timDanhSachChiTietPhieuNhapTheoMaPhieuNhap($thongTinPhieuNhap->id_purchase_order); //tim danh sach chi tiet phieu nhap
            // ***********Xu ly chi tiet phieu nhap
            if (!empty($request->chiTietPhieuNhap)) {
                for ($i = 0; $i < count($request->chiTietPhieuNhap); $i++) {
                    if (!empty($request->chiTietPhieuNhap[$i]) && !empty($request->soLuong[$i]) && $request->donGia[$i] >= 0) {
                        $thongTinSanPham = explode(' | ', $request->chiTietPhieuNhap[$i]);
                        if (empty($thongTinSanPham[0]) || empty($thongTinSanPham[1])) { // thong tin san pham nhap vao sai cu phap quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $maSanPham = explode('SP', $thongTinSanPham[0]);
                        $maSanPham = $maSanPham[1];
                        $tenSanPham = $thongTinSanPham[1];
                        if (!is_numeric($maSanPham)) { // ma san pham nhap vao khong phai ky tu so quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($maSanPham);
                        if (empty($thongTinSanPham)) { // khong tim thay san pham tren database quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        if ($tenSanPham != $thongTinSanPham->name_products) { // thong tin san pham khong khop va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $soLuongNhap = $request->soLuong[$i];
                        $donGiaNhap = explode(',', $request->donGia[$i]);
                        $temp = "";
                        foreach ($donGiaNhap as $dgn) {
                            $temp = $temp . $dgn;
                        }
                        $donGiaNhap = $temp;
                        if (!is_numeric($donGiaNhap)) { // so tien don gia nhap vao khong phai ky tu so quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền đơn giá nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }

                        $dataChiTietPhieuNhap = [
                            NULL, //machitietphieunhap tu dong
                            $thongTinPhieuNhap->id_purchase_order,
                            $thongTinSanPham->id_products,
                            $soLuongNhap,
                            $donGiaNhap
                        ];
                        $this->chiTietPhieuNhap->themChiTietPhieuNhap($dataChiTietPhieuNhap); //them chi tiet phieu nhap vao database
                        // Xu ly so luong va gia san pham
                        $giaNhap = 0;
                        if ($thongTinSanPham->entry_price > $donGiaNhap) { //gia nhap moi cu hon gia nhap moi thi lay gia nhap cu
                            $giaNhap = $thongTinSanPham->entry_price;
                        } else {
                            $giaNhap = $donGiaNhap;
                        }

                        $giaBan = 0;
                        if ($giaNhap >= $thongTinSanPham->sale_price) { //gia nhap lon hon gia ban cu sua lai gia ban moi bang gia nhap + them loi 30% tren gia nhap
                            $giaBan = $giaNhap * (1 + 30 / 100);
                        } else {
                            $giaBan = $thongTinSanPham->sale_price;
                        }

                        $giaKhuyenMai = NULL;
                        if (!empty($thongTinSanPham->promotional_price)) {
                            if ($giaNhap >= $thongTinSanPham->promotional_price) { //gia nhap lon hon gia khuyen mai cu sua lai bo luon gia khuyen mai
                                $giaKhuyenMai = NULL;
                            } else {
                                $giaKhuyenMai = $thongTinSanPham->promotional_price;
                            }
                        }
                        $dataSanPham = [
                            $thongTinSanPham->qty + $soLuongNhap, //them so luong vua nhap vao ton kho
                            $giaNhap,
                            $giaBan,
                            $giaKhuyenMai
                        ];
                        $this->sanPham->nhapHang($dataSanPham, $thongTinSanPham->id_products); //them so luong ton kho va chinh gia database
                    }
                }
            }
            if (!empty($danhSachChiTietPhieuNhap)) {
                foreach ($danhSachChiTietPhieuNhap as $ctpn) {
                    $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($ctpn->id_products); //tim san pham can chinh so luong
                    if (!empty($thongTinSanPham)) {
                        $dataSanPham = [
                            $thongTinSanPham->qty - $ctpn->qty
                        ];
                        $this->sanPham->suaSoLuong($dataSanPham, $thongTinSanPham->id_products); //chinh so luong ton kho san pham tren database
                        $this->chiTietPhieuNhap->xoaChiTietPhieuNhap($ctpn->id_purchase_order_details); //xoa chi tiet phieu nhap tren database
                    }
                }
            }
            return redirect()->route('phieunhap')->with('tieudethongbao', 'Thao tác thành công')->with('thongbao', 'Sửa thông tin phiếu nhập thành công')->with('loaithongbao', 'success');
        }
        if ($request->thaoTac == "thêm phiếu nhập") { // *******************************************************************************************them phieu nhap
            $rules = [
                'chiTietPhieuNhap' => 'array',
                'chiTietPhieuNhap.*' => 'required|string|max:255|min:3',
                'soLuong' => 'array',
                'soLuong.*' => 'required|integer',
                'donGia' => 'array',
                'donGia.*' => 'required|string|max:255|min:1',
                'thongTinNguoiDung' => 'required|string|max:255|min:3',
                'ghiChu' => 'max:255',
                'tongTien' => 'required|numeric',
                'daThanhToan' => 'required|string|max:255|min:1'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute nhập sai',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối thiểu :max ký tự',
                'integer' => ':attribute nhập sai',
                'numeric' => ':attribute nhập sai',
                'array' => ':attribute nhập sai'
            ];
            $attributes = [
                'chiTietPhieuNhap' => 'Chi tiết phiếu nhập',
                'chiTietPhieuNhap.*' => 'Chi tiết phiếu nhập *',
                'soLuong' => 'Số lượng',
                'soLuong.*' => 'Số lượng *',
                'donGia' => 'Đơn giá',
                'donGia.*' => 'Đơn giá *',
                'thongTinNguoiDung' => 'Thông tin người dùng',
                'ghiChu' => 'Ghi chú',
                'tongTien' => 'Tổng tiền',
                'daThanhToan' => 'Đã thanh toán'
            ];
            $request->validate($rules, $messages, $attributes);
            // ***********Xu ly them phieu nhap
            $thongTinNguoiDung = explode(' | ', $request->thongTinNguoiDung);
            if (empty($thongTinNguoiDung[0]) || empty($thongTinNguoiDung[1]) || empty($thongTinNguoiDung[2])) { // thong tin nguoi dung nhap vao sai cu phap quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin người dùng không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $maNguoiDung = explode('ND', $thongTinNguoiDung[0]);
            $maNguoiDung = $maNguoiDung[1];
            $hoTen = $thongTinNguoiDung[1];
            $soDienThoai = $thongTinNguoiDung[2];

            if (!is_numeric($maNguoiDung)) { // ma nguoi dung nhap vao khong phai ky tu so quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin người dùng không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoMa($maNguoiDung);
            if (empty($thongTinNguoiDung)) { // khong tim thay nguoi dung tren database quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin người dùng không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            if ($hoTen != $thongTinNguoiDung->name_users || $soDienThoai != $thongTinNguoiDung->phone) { // thong tin nguoi dung khong khop va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin người dùng không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            if ($thongTinNguoiDung->status == 0) { // thong tin nguoi dung dang bi khoa
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Trạng thái người dùng đang bị khóa, không thể thao tác!')->with('loaithongbao', 'danger');
            }
            $soTienDaThanhToan = explode(',', $request->daThanhToan);
            $temp = "";
            foreach ($soTienDaThanhToan as $stdtt) {
                $temp = $temp . $stdtt;
            }
            $soTienDaThanhToan = $temp;
            if (!is_numeric($soTienDaThanhToan)) { // so tien da thanh toan nhap vao khong phai ky tu so quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền đã thanh toán nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            if ($soTienDaThanhToan == 0 && $request->tongTien == 0) { // phieu khong co gi nen khong lap va quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền đã thanh toán nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $congNo = $soTienDaThanhToan - $request->tongTien;
            $ngayTao = date("Y-m-d H:i:s");

            $dataPhieuNhap = [
                NULL, //maphieunhap tu dong
                $thongTinNguoiDung->id_users,
                $request->ghiChu,
                $request->tongTien,
                $congNo,
                $ngayTao
            ];
            $this->phieuNhap->themPhieuNhap($dataPhieuNhap); //them phieu nhap vao database
            $thongTinPhieuNhap = $this->phieuNhap->timPhieuNhapTheoNgayTao($ngayTao); //tim qua tang vua them
            // ***********Xu ly them chi tiet phieu nhap
            if (!empty($request->chiTietPhieuNhap)) {
                for ($i = 0; $i < count($request->chiTietPhieuNhap); $i++) {
                    if (!empty($request->chiTietPhieuNhap[$i]) && !empty($request->soLuong[$i]) && $request->donGia[$i] >= 0) {
                        $thongTinSanPham = explode(' | ', $request->chiTietPhieuNhap[$i]);
                        if (empty($thongTinSanPham[0]) || empty($thongTinSanPham[1])) { // thong tin san pham nhap vao sai cu phap quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $maSanPham = explode('SP', $thongTinSanPham[0]);
                        $maSanPham = $maSanPham[1];
                        $tenSanPham = $thongTinSanPham[1];
                        if (!is_numeric($maSanPham)) { // ma san pham nhap vao khong phai ky tu so quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($maSanPham);
                        if (empty($thongTinSanPham)) { // khong tim thay san pham tren database quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        if ($tenSanPham != $thongTinSanPham->name_products) { // thong tin san pham khong khop va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $soLuongNhap = $request->soLuong[$i];
                        $donGiaNhap = explode(',', $request->donGia[$i]);
                        $temp = "";
                        foreach ($donGiaNhap as $dgn) {
                            $temp = $temp . $dgn;
                        }
                        $donGiaNhap = $temp;
                        if (!is_numeric($donGiaNhap)) { // so tien don gia nhap vao khong phai ky tu so quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền đơn giá nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $dataChiTietPhieuNhap = [
                            NULL, //machitietphieunhap tu dong
                            $thongTinPhieuNhap->id_purchase_order,
                            $thongTinSanPham->id_products,
                            $soLuongNhap,
                            $donGiaNhap
                        ];
                        $this->chiTietPhieuNhap->themChiTietPhieuNhap($dataChiTietPhieuNhap); //them chi tiet phieu nhap vao database
                        // Xu ly so luong va gia san pham
                        $giaNhap = 0;
                        if ($thongTinSanPham->entry_price > $donGiaNhap) { //gia nhap moi cu hon gia nhap moi thi lay gia nhap cu
                            $giaNhap = $thongTinSanPham->entry_price;
                        } else {
                            $giaNhap = $donGiaNhap;
                        }

                        $giaBan = 0;
                        if ($giaNhap >= $thongTinSanPham->sale_price) { //gia nhap lon hon gia ban cu sua lai gia ban moi bang gia nhap + them loi 30% tren gia nhap
                            $giaBan = $giaNhap * (1 + 30 / 100);
                        } else {
                            $giaBan = $thongTinSanPham->sale_price;
                        }

                        $giaKhuyenMai = NULL;
                        if (!empty($thongTinSanPham->promotional_price)) {
                            if ($giaNhap >= $thongTinSanPham->promotional_price) { //gia nhap lon hon gia khuyen mai cu sua lai bo luon gia khuyen mai
                                $giaKhuyenMai = NULL;
                            } else {
                                $giaKhuyenMai = $thongTinSanPham->promotional_price;
                            }
                        }
                        $dataSanPham = [
                            $thongTinSanPham->qty + $soLuongNhap, //them so luong vua nhap vao ton kho
                            $giaNhap,
                            $giaBan,
                            $giaKhuyenMai
                        ];
                        $this->sanPham->nhapHang($dataSanPham, $thongTinSanPham->id_products); //them so luong ton kho va chinh gia database
                    }
                }
            }
            return redirect()->route('phieunhap')->with('tieudethongbao', 'Thao tác thành công')->with('thongbao', 'Lập phiếu nhập thành công')->with('loaithongbao', 'success');
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
