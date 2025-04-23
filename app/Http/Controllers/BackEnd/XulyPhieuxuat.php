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

class XulyPhieuxuat extends Controller
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
    public function xulyphieuxuat(Request $request)
    {
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "đổi tình trạng giao hàng") { // *******************************************************************************************doi tinh trang giao hang phieu xuat
            $rules = [
                'maPhieuXuatDoi' => 'required|integer|exists:invoice,id_invoice'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'integer' => ':attribute đã nhập sai'
            ];
            $attributes = [
                'maPhieuXuatDoi' => 'Mã phiếu xuất'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinPhieuXuat = $this->phieuXuat->timPhieuXuatTheoMa($request->maPhieuXuatDoi); //tim phieu xuat can doi
            if (!empty($thongTinPhieuXuat)) {
                $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoMa($thongTinPhieuXuat->id_users);
                if ($thongTinNguoiDung->status == 0) { // thong tin nguoi dung dang bi khoa
                    return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Trạng thái người dùng đang bị khóa, không thể thao tác!')->with('loaithongbao', 'danger');
                }
                if ($thongTinPhieuXuat->delivery_status >= 4) $thongTinPhieuXuat->delivery_status = 0;
                else $thongTinPhieuXuat->delivery_status++;
                $dataPhieuXuat = [
                    $thongTinPhieuXuat->delivery_status
                ];
                $this->phieuXuat->doiTinhTrangGiaoHangPhieuXuat($dataPhieuXuat, $thongTinPhieuXuat->id_invoice); //doi tinh trang giao hang phieu xuat tren database
                $danhSachChiTietPhieuXuat = $this->chiTietPhieuXuat->timDanhSachChiTietPhieuXuatTheoMaPhieuXuat($thongTinPhieuXuat->id_invoice); //chinh ton kho
                if (!empty($danhSachChiTietPhieuXuat)) {
                    foreach ($danhSachChiTietPhieuXuat as $ctpx) {
                        $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($ctpx->id_products); //tim san pham can chinh so luong
                        if (!empty($thongTinSanPham) && $thongTinPhieuXuat->delivery_status == 0) {
                            $dataSanPham = [
                                $thongTinSanPham->qty + $ctpx->qty
                            ];
                            $this->sanPham->suaSoLuong($dataSanPham, $thongTinSanPham->id_products); //chinh so luong ton kho san pham tren database
                        }
                        if (!empty($thongTinSanPham) && $thongTinPhieuXuat->delivery_status == 4) {
                            $dataSanPham = [
                                $thongTinSanPham->qty - $ctpx->qty
                            ];
                            $this->sanPham->suaSoLuong($dataSanPham, $thongTinSanPham->id_products); //chinh so luong ton kho san pham tren database
                        }
                    }
                }
                return back()->with(
                    'tieudethongbao',
                    'Thao tác thành công'
                )->with(
                    'thongbao',
                    'Đổi tình trạng giao hàng phiếu xuất thành công'
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
                'Đổi tình trạng giao hàng phiếu xuất thất bại'
            )->with(
                'loaithongbao',
                'danger'
            );
        }
        if ($request->thaoTac == "xóa phiếu xuất") { // *******************************************************************************************xoa phieu xuat
            $rules = [
                'maPhieuXuatXoa' => 'required|integer|exists:invoice,id_invoice'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'integer' => ':attribute đã nhập sai'
            ];
            $attributes = [
                'maPhieuXuatXoa' => 'Mã phiếu xuất'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinPhieuXuat = $this->phieuXuat->timPhieuXuatTheoMa($request->maPhieuXuatXoa); //tim phieu xuat can xoa
            if (!empty($thongTinPhieuXuat)) {
                $danhSachChiTietPhieuXuat = $this->chiTietPhieuXuat->timDanhSachChiTietPhieuXuatTheoMaPhieuXuat($thongTinPhieuXuat->id_invoice); //tim chi tiet phieu xuat can xoa
                if (!empty($danhSachChiTietPhieuXuat)) {
                    foreach ($danhSachChiTietPhieuXuat as $ctpx) {
                        $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($ctpx->id_products); //tim san pham can chinh so luong
                        if (!empty($thongTinSanPham) && $thongTinPhieuXuat->delivery_status == 4) {
                            $dataSanPham = [
                                $thongTinSanPham->qty + $ctpx->qty
                            ];
                            $this->sanPham->suaSoLuong($dataSanPham, $thongTinSanPham->id_products); //chinh so luong ton kho san pham tren database
                        }
                        $this->chiTietPhieuXuat->xoaChiTietPhieuXuat($ctpx->id_invoice_details); //xoa chi tiet phieu xuat tren database
                    }
                }
                $this->phieuXuat->xoaPhieuXuat($thongTinPhieuXuat->id_invoice); //xoa phieu xuat tren database
                return back()->with(
                    'tieudethongbao',
                    'Thao tác thành công'
                )->with(
                    'thongbao',
                    'Xóa phiếu xuất thành công'
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
                'Xóa phiếu xuất thất bại'
            )->with(
                'loaithongbao',
                'danger'
            );
        }
        if ($request->thaoTac == "sửa phiếu xuất") { // *******************************************************************************************sua phieu xuat
            $rules = [
                'maPhieuXuatSua' => 'required|integer|exists:invoice,id_invoice',
                'chiTietPhieuXuat' => 'required|array',
                'chiTietPhieuXuat.*' => 'required|string|max:255|min:3',
                'soLuong' => 'required|array',
                'soLuong.*' => 'required|integer',
                'baoHanh' => 'required|array',
                'baoHanh.*' => 'required|integer',
                'donGia' => 'required|array',
                'donGia.*' => 'required|string|max:255|min:1',
                'thongTinNguoiDung' => 'required|string|max:255|min:3',
                'tongTien' => 'required|numeric',
                'daThanhToan' => 'required|string|max:255|min:1',
                'hinhThucThanhToan' => 'required|integer|between:0,2',
                'tinhTrangGiaoHang' => 'required|integer|between:0,4',
                'ghiChu' => 'max:255'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute đã nhập sai',
                'integer' => ':attribute đã nhập sai',
                'numeric' => ':attribute đã nhập sai',
                'between' => ':attribute vượt quá số lượng cho phép',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'digits' => ':attribute không đúng :digits ký tự'
            ];
            $attributes = [
                'maPhieuXuatSua' => 'Mã phiếu xuất',
                'chiTietPhieuXuat' => 'Chi tiết phiếu xuất',
                'chiTietPhieuXuat.*' => 'Chi tiết phiếu xuất *',
                'soLuong' => 'Số lượng',
                'soLuong.*' => 'Số lượng *',
                'baoHanh' => 'Bảo hành',
                'baoHanh.*' => 'Bảo hành *',
                'donGia' => 'Đơn giá',
                'donGia.*' => 'Đơn giá *',
                'thongTinNguoiDung' => 'Thông tin người dùng',
                'tongTien' => 'Tổng tiền',
                'daThanhToan' => 'Đã thanh toán',
                'hinhThucThanhToan' => 'Hình thức thanh toán',
                'tinhTrangGiaoHang' => 'Tình trạng giao hàng',
                'ghiChu' => 'Ghi chú'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinPhieuXuat = $this->phieuXuat->timPhieuXuatTheoMa($request->maPhieuXuatSua); //tim phieu xuat
            // ***********Xu ly phieu xuat
            $thongTinNguoiDung = explode(' | ', $request->thongTinNguoiDung);
            if (empty($thongTinNguoiDung[0]) || empty($thongTinNguoiDung[1]) || empty($thongTinNguoiDung[2])) { // thong tin nguoi dung nhap vao sai cu phap quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa thông tin khách hàng, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $maNguoiDung = explode('ND', $thongTinNguoiDung[0]);
            $maNguoiDung = $maNguoiDung[1];
            $hoTen = $thongTinNguoiDung[1];
            $soDienThoai = $thongTinNguoiDung[2];
            if (!is_numeric($maNguoiDung)) { // ma nguoi dung nhap vao khong phai ky tu so quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa thông tin khách hàng, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            if ($thongTinPhieuXuat->id_users != $maNguoiDung) { // thong tin nguoi dung khong khop va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa thông tin khách hàng, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoMa($maNguoiDung);
            if (empty($thongTinNguoiDung)) { // khong tim thay nguoi dung tren database quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa thông tin khách hàng, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            if ($hoTen != $thongTinNguoiDung->name_users || $soDienThoai != $thongTinNguoiDung->phone) { // thong tin nguoi dung khong khop va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Không thể chỉnh sửa thông tin khách hàng, vui lòng nhập lại!')->with('loaithongbao', 'danger');
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
            if (($soTienDaThanhToan == 0 && $request->tongTien == 0) || $soTienDaThanhToan > $request->tongTien) { // phieu khong co gi nen khong lap va quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền đã thanh toán nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $hoTenNguoiNhan = $thongTinPhieuXuat->name_receiver;
            $soDienThoaiNguoiNhan = $thongTinPhieuXuat->phone_receiver;
            $diaChiNguoiNhan = $thongTinPhieuXuat->address_receiver;
            $ghiChu = $thongTinPhieuXuat->note;
            $tongTien = $thongTinPhieuXuat->total_money;
            $tinhTrangGiaoHang = $thongTinPhieuXuat->delivery_status;
            $hinhThucThanhToan = $thongTinPhieuXuat->payments;
            $congNo = $thongTinPhieuXuat->debt;
            $congNoSua = $soTienDaThanhToan - $request->tongTien;
            if(!empty($thongTinPhieuXuat->id_discount)){
                $thongTinMaGiamGia = $this->maGiamGia->timMaGiamGiaTheoMa($thongTinPhieuXuat->id_discount); //tim ma giam gia
                if (!empty($thongTinMaGiamGia)) {
                    $congNoSua += $thongTinMaGiamGia->reduced_price;
                } else {
                    return back()->with('thongbao', 'Mã giảm giá không tồn tại!');
                }
            }
            if (isset($request->thongTinNguoiNhanKhac)) {
                if ($request->thongTinNguoiNhanKhac == "on") {
                    $rules = [
                        'hoTenNguoiNhan' => 'required|string|max:50|min:3',
                        'soDienThoaiNguoiNhan' => 'required|numeric|digits:10',
                        'diaChiNguoiNhan' => 'required|string|max:255|min:3',
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
                        'hoTenNguoiNhan' => 'Họ tên người nhận',
                        'soDienThoaiNguoiNhan' => 'Số điện thoại người nhận',
                        'diaChiNguoiNhan' => 'Địa chỉ người nhận',
                    ];
                    $request->validate($rules, $messages, $attributes);
                    if ($request->hoTenNguoiNhan != $hoTenNguoiNhan) { //ho ten nguoi nhan vua chinh sua khac voi ho ten nguoi nhan cu
                        $hoTenNguoiNhan = $request->hoTenNguoiNhan;
                    }
                    if ($request->soDienThoaiNguoiNhan != $soDienThoaiNguoiNhan) { //sdt nguoi nhan vua chinh sua khac voi sdt nguoi nhan cu
                        $soDienThoaiNguoiNhan = $request->soDienThoaiNguoiNhan;
                    }
                    if ($request->diaChiNguoiNhan != $diaChiNguoiNhan) { //dia chi nguoi nhan vua chinh sua khac voi dia chi nguoi nhan cu
                        $diaChiNguoiNhan = $request->diaChiNguoiNhan;
                    }
                } else {
                    return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin người nhận khác nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                }
            } else { //neu khong tich chon giao den noi khac
                $hoTenNguoiNhan = $thongTinNguoiDung->name_users;
                $soDienThoaiNguoiNhan = $thongTinNguoiDung->phone;
                $diaChiNguoiNhan = $thongTinNguoiDung->address;
                //lay thong tin nguoi dung dat hang lam thong tin giao hang
            }
            if ($request->ghiChu != $ghiChu) { //ghi chu vua chinh sua khac voi ghi chu cu
                $ghiChu = $request->ghiChu;
            }
            if ($request->tongTien != $tongTien) { //tong tien vua chinh sua khac voi tong tien cu
                $tongTien = $request->tongTien;
            }
            if ($request->tinhTrangGiaoHang != $tinhTrangGiaoHang) { //tinh trang giao hang vua chinh sua khac voi tinh trang giao hang cu
                $tinhTrangGiaoHang = $request->tinhTrangGiaoHang;
            }
            if ($request->hinhThucThanhToan != $hinhThucThanhToan) { //hinh thuc thanh toan vua chinh sua khac voi hinh thuc thanh toan cu
                $hinhThucThanhToan = $request->hinhThucThanhToan;
            }
            if ($congNoSua != $congNo) { //ghi chu vua chinh sua khac voi ghi chu cu
                $congNo = $congNoSua;
            }
            $dataPhieuXuat = [
                $hoTenNguoiNhan,
                $soDienThoaiNguoiNhan,
                $diaChiNguoiNhan,
                $ghiChu,
                $tongTien,
                $tinhTrangGiaoHang,
                $hinhThucThanhToan,
                $congNo
            ];
            $this->phieuXuat->suaPhieuXuat($dataPhieuXuat, $thongTinPhieuXuat->id_invoice); //sua phieu xuat tren database
            $danhSachChiTietPhieuXuat = $this->chiTietPhieuXuat->timDanhSachChiTietPhieuXuatTheoMaPhieuXuat($thongTinPhieuXuat->id_invoice); //tim danh sach chi tiet phieu xuat
            // ***********Xu ly them chi tiet phieu xuat
            if (!empty($request->chiTietPhieuXuat)) {
                for ($i = 0; $i < count($request->chiTietPhieuXuat); $i++) {
                    if (!empty($request->chiTietPhieuXuat[$i]) && $request->soLuong[$i] > 0 && $request->donGia[$i] >= 0 && $request->baoHanh[$i] >= 0) {
                        $thongTinSanPham = explode(' | ', $request->chiTietPhieuXuat[$i]);
                        if (empty($thongTinSanPham[0]) || empty($thongTinSanPham[1])) { // thong tin san pham xuat  sai cu phap quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $maSanPham = explode('SP', $thongTinSanPham[0]);
                        $maSanPham = $maSanPham[1];
                        $tenSanPham = $thongTinSanPham[1];
                        if (!is_numeric($maSanPham)) { // ma san pham xuat  khong phai ky tu so quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($maSanPham);
                        if (empty($thongTinSanPham)) { // khong tim thay san pham tren database quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        if ($tenSanPham != $thongTinSanPham->name_products) { // thong tin san pham khong khop va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $soLuongXuat = $request->soLuong[$i];
                        $baoHanhXuat = $request->baoHanh[$i];
                        $donGiaXuat = explode(',', $request->donGia[$i]);
                        $temp = "";
                        foreach ($donGiaXuat as $dgx) {
                            $temp = $temp . $dgx;
                        }
                        $donGiaXuat = $temp;
                        if (!is_numeric($donGiaXuat)) { // so tien don gia xuat  khong phai ky tu so quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền đơn giá nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $dataChiTietPhieuXuat = [
                            NULL, //machitietphieuxuat tu dong
                            $thongTinPhieuXuat->id_invoice,
                            $thongTinSanPham->id_products,
                            $baoHanhXuat,
                            $soLuongXuat,
                            $donGiaXuat
                        ];
                        $this->chiTietPhieuXuat->themChiTietPhieuXuat($dataChiTietPhieuXuat); //them chi tiet phieu xuat vao database
                        // Xu ly so luong san pham
                        if ($request->tinhTrangGiaoHang == 4) { //phieu xuat da giao hang moi tru vao ton kho
                            $dataSanPham = [
                                $thongTinSanPham->qty - $soLuongXuat, //tru so luong vua xuat vao ton kho
                            ];
                            $this->sanPham->suaSoLuong($dataSanPham, $thongTinSanPham->id_products); //them so luong ton kho va chinh gia database
                        }
                    }
                }
                if (!empty($danhSachChiTietPhieuXuat)) {
                    foreach ($danhSachChiTietPhieuXuat as $ctpx) {
                        $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($ctpx->id_products); //tim san pham can chinh so luong
                        if (!empty($thongTinSanPham) && $thongTinPhieuXuat->delivery_status == 4) {
                            $dataSanPham = [
                                $thongTinSanPham->qty + $ctpx->qty
                            ];
                            $this->sanPham->suaSoLuong($dataSanPham, $thongTinSanPham->id_products); //chinh so luong ton kho san pham tren database
                        }
                        $this->chiTietPhieuXuat->xoaChiTietPhieuXuat($ctpx->id_invoice_details); //xoa chi tiet phieu xuat tren database
                    }
                }
                return redirect()->route('phieuxuat')->with('tieudethongbao', 'Thao tác thành công')->with('thongbao', 'Sửa thông tin phiếu xuất thành công')->with('loaithongbao', 'success');
            }
            return redirect()->route('phieuxuat')->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Sửa thông tin phiếu xuất thất bại')->with('loaithongbao', 'danger');
        }
        if ($request->thaoTac == "thêm phiếu xuất") { // *******************************************************************************************them phieu xuat
            $rules = [
                'chiTietPhieuXuat' => 'required|array',
                'chiTietPhieuXuat.*' => 'required|string|max:255|min:3',
                'soLuong' => 'required|array',
                'soLuong.*' => 'required|integer',
                'baoHanh' => 'required|array',
                'baoHanh.*' => 'required|integer',
                'donGia' => 'required|array',
                'donGia.*' => 'required|string|max:255|min:1',
                'thongTinNguoiDung' => 'required|string|max:255|min:3',
                'tongTien' => 'required|numeric',
                'daThanhToan' => 'required|string|max:255|min:1',
                'hinhThucThanhToan' => 'required|integer|between:0,2',
                'tinhTrangGiaoHang' => 'required|integer|between:0,4',
                'ghiChu' => 'max:255'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute đã nhập sai',
                'integer' => ':attribute đã nhập sai',
                'numeric' => ':attribute đã nhập sai',
                'between' => ':attribute vượt quá số lượng cho phép',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'digits' => ':attribute không đúng :digits ký tự'
            ];
            $attributes = [
                'chiTietPhieuXuat' => 'Chi tiết phiếu xuất',
                'chiTietPhieuXuat.*' => 'Chi tiết phiếu xuất *',
                'soLuong' => 'Số lượng',
                'soLuong.*' => 'Số lượng *',
                'baoHanh' => 'Bảo hành',
                'baoHanh.*' => 'Bảo hành *',
                'donGia' => 'Đơn giá',
                'donGia.*' => 'Đơn giá *',
                'thongTinNguoiDung' => 'Thông tin người dùng',
                'tongTien' => 'Tổng tiền',
                'daThanhToan' => 'Đã thanh toán',
                'hinhThucThanhToan' => 'Hình thức thanh toán',
                'tinhTrangGiaoHang' => 'Tình trạng giao hàng',
                'ghiChu' => 'Ghi chú'
            ];
            $request->validate($rules, $messages, $attributes);
            // ***********Xu ly them phieu xuat
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
            if (($soTienDaThanhToan == 0 && $request->tongTien == 0) || $soTienDaThanhToan > $request->tongTien) { // phieu khong co gi nen khong lap va quay lai trang truoc va bao loi
                return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền đã thanh toán nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
            }
            $congNo = $soTienDaThanhToan - $request->tongTien;
            $ngayTao = date("Y-m-d H:i:s");
            $dataPhieuXuat = [
                NULL, //maphieuxuat tu dong
                $thongTinNguoiDung->name_users,    // hotennguoinhan,
                $thongTinNguoiDung->phone,    // sodienthoainguoinhan,
                $thongTinNguoiDung->address,    // diachinguoinhan,
                $thongTinNguoiDung->id_users,
                NULL,    // magiamgia,
                $request->ghiChu,
                $request->tongTien,
                $request->tinhTrangGiaoHang,    // tinhtranggiaohang,  	0 là đã hủy, 1 là chờ xác nhận, 2 là đang chuẩn bị hàng, 3 là đang giao, 4 là đã giao thành công
                $request->hinhThucThanhToan,    // hinhthucthanhtoan,   0 là tiền mặt, 1 là chuyển khoản, 2 là atm qua vpn
                $congNo,    // congno, 0 là đã thanh toán, !=0 là công nợ
                $ngayTao    // ngaytao
            ];
            if (isset($request->thongTinNguoiNhanKhac)) {
                if ($request->thongTinNguoiNhanKhac == "on") {
                    $rules = [
                        'hoTenNguoiNhan' => 'required|string|max:50|min:3',
                        'soDienThoaiNguoiNhan' => 'required|numeric|digits:10',
                        'diaChiNguoiNhan' => 'required|string|max:255|min:3',
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
                        'hoTenNguoiNhan' => 'Họ tên người nhận',
                        'soDienThoaiNguoiNhan' => 'Số điện thoại người nhận',
                        'diaChiNguoiNhan' => 'Địa chỉ người nhận',
                    ];
                    $request->validate($rules, $messages, $attributes);
                    $dataPhieuXuat = [
                        NULL, //maphieuxuat tu dong
                        $request->hoTenNguoiNhan,    // hotennguoinhan,
                        $request->soDienThoaiNguoiNhan,    // sodienthoainguoinhan,
                        $request->diaChiNguoiNhan,    // diachinguoinhan,
                        $thongTinNguoiDung->id_users,
                        NULL,    // magiamgia,
                        $request->ghiChu,
                        $request->tongTien,
                        $request->tinhTrangGiaoHang,    // tinhtranggiaohang,  	0 là đã hủy, 1 là chờ xác nhận, 2 là đang chuẩn bị hàng, 3 là đang giao, 4 là đã giao thành công
                        $request->hinhThucThanhToan,    // hinhthucthanhtoan,   0 là tiền mặt, 1 là chuyển khoản, 2 là atm qua vpn
                        $congNo,    // congno, 0 là đã thanh toán, !=0 là công nợ
                        $ngayTao    // ngaytao
                    ];
                } else {
                    return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin người nhận khác nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                }
            }
            $this->phieuXuat->themPhieuXuat($dataPhieuXuat); //them phieu xuat vao database
            $thongTinPhieuXuat = $this->phieuXuat->timPhieuXuatTheoNgayTao($ngayTao); //tim phieu xuat vua them
            // ***********Xu ly them chi tiet phieu xuat
            if (!empty($request->chiTietPhieuXuat)) {
                for ($i = 0; $i < count($request->chiTietPhieuXuat); $i++) {
                    if (!empty($request->chiTietPhieuXuat[$i]) && $request->soLuong[$i] > 0 && $request->donGia[$i] >= 0 && $request->baoHanh[$i] >= 0) {
                        $thongTinSanPham = explode(' | ', $request->chiTietPhieuXuat[$i]);
                        if (empty($thongTinSanPham[0]) || empty($thongTinSanPham[1])) { // thong tin san pham xuat  sai cu phap quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $maSanPham = explode('SP', $thongTinSanPham[0]);
                        $maSanPham = $maSanPham[1];
                        $tenSanPham = $thongTinSanPham[1];
                        if (!is_numeric($maSanPham)) { // ma san pham xuat  khong phai ky tu so quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($maSanPham);
                        if (empty($thongTinSanPham)) { // khong tim thay san pham tren database quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        if ($tenSanPham != $thongTinSanPham->name_products) { // thong tin san pham khong khop va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Thông tin sản phẩm không tồn tại, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $soLuongXuat = $request->soLuong[$i];
                        $baoHanhXuat = $request->baoHanh[$i];
                        $donGiaXuat = explode(',', $request->donGia[$i]);
                        $temp = "";
                        foreach ($donGiaXuat as $dgx) {
                            $temp = $temp . $dgx;
                        }
                        $donGiaXuat = $temp;
                        if (!is_numeric($donGiaXuat)) { // so tien don gia xuat  khong phai ky tu so quay lai trang truoc va bao loi
                            return back()->with('tieudethongbao', 'Thao tác thất bại')->with('thongbao', 'Số tiền đơn giá nhập sai, vui lòng nhập lại!')->with('loaithongbao', 'danger');
                        }
                        $dataChiTietPhieuXuat = [
                            NULL, //machitietphieuxuat tu dong
                            $thongTinPhieuXuat->id_invoice,
                            $thongTinSanPham->id_products,
                            $baoHanhXuat,
                            $soLuongXuat,
                            $donGiaXuat
                        ];
                        $this->chiTietPhieuXuat->themChiTietPhieuXuat($dataChiTietPhieuXuat); //them chi tiet phieu xuat vao database
                        // Xu ly so luong san pham
                        if ($request->tinhTrangGiaoHang == 4) { //phieu xuat da giao hang moi tru vao ton kho
                            $dataSanPham = [
                                $thongTinSanPham->qty - $soLuongXuat, //tru so luong vua xuat vao ton kho
                            ];
                            $this->sanPham->suaSoLuong($dataSanPham, $thongTinSanPham->id_products); //them so luong ton kho va chinh gia database
                        }
                    }
                }
            }
            return redirect()->route('phieuxuat')->with('tieudethongbao', 'Thao tác thành công')->with('thongbao', 'Lập phiếu xuất thành công')->with('loaithongbao', 'success');
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
