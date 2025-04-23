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

class ThanhToanController extends Controller
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
    public function thanhtoan(Request $request)
    {
        if (empty(session('gioHang'))) return redirect()->route('giohang');
        if (isset($request->vnp_ResponseCode) && isset($request->vnp_TransactionStatus)) {// sau khi thanh toan vnpay thanh cong
            if ($request->vnp_ResponseCode == "00" && $request->vnp_TransactionStatus == "00") {
                $dataPhieuXuat = json_decode($request->vnp_OrderInfo);
                $dataPhieuXuat[10] += ($request->vnp_Amount / 100); // cong no
                $this->phieuXuat->themPhieuXuat($dataPhieuXuat); //them phieu xuat vao database
                $thongTinPhieuXuat = $this->phieuXuat->timPhieuXuatTheoNgayTao($dataPhieuXuat[11]); //tim phieu xuat vua them
                foreach (session('gioHang') as $ctgh) {
                    $donGia = $ctgh['sale_price'];
                    if (!empty($ctgh['pormotional_price'])) {
                        $donGia = $ctgh['pormotional_price'];
                    }
                    if (!empty($ctgh['gift'])) { // xem chi tiet gio hang san pham do co qua tang khong neu co qua tang xuat them chi tiet phieu xuat 0 dong
                        foreach ($ctgh['gift'] as $thongTinSanPham) {
                            $dataChiTietPhieuXuat = [
                                NULL, //machitietphieuxuat  tu dong
                                $thongTinPhieuXuat->id_invoice,
                                $thongTinSanPham->id_products,
                                $thongTinSanPham->guarantee,
                                $ctgh['soluongmua'], //so luong qua tang theo so luong mua cua san pham
                                0 //don gia qua tang la 0 dong
                            ];
                            $this->chiTietPhieuXuat->themChiTietPhieuXuat($dataChiTietPhieuXuat); //them chi tiet phieu xuat vao database
                        }
                    }
                    $dataChiTietPhieuXuat = [
                        NULL, //machitietphieuxuat  tu dong
                        $thongTinPhieuXuat->id_invoice,
                        $ctgh['id_products'],
                        $ctgh['guarantee'],
                        $ctgh['soluongmua'],
                        $donGia
                    ];
                    $this->chiTietPhieuXuat->themChiTietPhieuXuat($dataChiTietPhieuXuat); //them chi tiet phieu xuat vao database
                }
                session()->forget('gioHang');
                return redirect()->route('/')->with('thongbao', 'Đặt hàng thành công, sẽ có nhân viên giao hàng cho bạn trong 24h tới!');
            }
            return redirect()->route('/')->with('thongbao', 'Thao tác thất bại vui lòng thử lại!');
        }
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        return view('user.thanhtoan', compact(
            'danhSachHangSanXuat'
        ));
    }
    public function xulythanhtoan(Request $request)
    {
        if (empty(session('gioHang'))) return redirect()->route('giohang');
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "đặt hàng") { // *******************************************************************************************dat hang // thanh toan
            $rules = [
                'hoTen' => 'required|string|max:50|min:3',
                'soDienThoai' => 'required|numeric|digits:10',
                'diaChi' => 'required|string|max:255|min:3',
                'tongTien' => 'required|numeric',
                'hinhThucThanhToan' => 'required|integer|between:0,2',
                'ghiChu' => 'max:255'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute đã nhập sai',
                'integer' => ':attribute đã nhập sai',
                'numeric' => ':attribute đã nhập sai',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'between' => ':attribute vượt quá số lượng cho phép',
                'digits' => ':attribute không đúng :digits ký tự'
            ];
            $attributes = [
                'hoTen' => 'Họ tên',
                'soDienThoai' => 'Số điện thoại',
                'diaChi' => 'Địa chỉ',
                'tongTien' => 'Tổng tiền',
                'hinhThucThanhToan' => 'Hình thức thanh toán',
                'ghiChu' => 'Ghi chú'
            ];
            $request->validate($rules, $messages, $attributes);
            if (isset($request->taoTaiKhoan)) {
                if ($request->taoTaiKhoan == "on") {
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
                } else {
                    return back()->with('thongbao', 'Đặt hàng thất bại!');
                }
            }
            if (isset($request->thongTinNguoiNhanKhac)) {
                if ($request->thongTinNguoiNhanKhac == "on") {
                    $rules = [
                        'hoTen' => 'required|string|max:50|min:3',
                        'soDienThoai' => 'required|numeric|digits:10',
                        'diaChi' => 'required|string|max:255|min:3',
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
                        'hoTenNguoiNhan' => 'required|string|max:50|min:3',
                        'soDienThoaiNguoiNhan' => 'required|numeric|digits:10',
                        'diaChiNguoiNhan' => 'required|string|max:255|min:3',
                    ];
                    $request->validate($rules, $messages, $attributes);
                } else {
                    return back()->with('thongbao', 'Đặt hàng thất bại!');
                }
            }
            $ngayTao = date("Y-m-d H:i:s");
            $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoSoDienThoai($request->soDienThoai); //tim nguoi dung da ton tai hay chua
            if (!empty($thongTinNguoiDung)) { //neu tim thay
                if ($thongTinNguoiDung->status == 0) { //neu nguoi dung dang bi khoa
                    return back()->with('thongbao', 'Thông tin người đặt hiện đang bị tạm khóa do hủy quá nhiều đơn!');
                }
                if (isset($request->taoTaiKhoan)) {
                    if ($request->taoTaiKhoan == "on") {
                        if (!empty($thongTinNguoiDung->email)) { //da co tai khoan nen khong the tao tai khoan moi
                            return back()->with('thongbao', 'Thông tin người đặt đã có tài khoản nên không thể tạo tài khoản!');
                        } else { //chua co tai khoan thi tao tai khoan
                            $dataNguoiDung = [
                                $request->email,
                                bcrypt($request->matKhau)
                            ];
                            $this->nguoiDung->taoTaiKhoanNguoiDung($dataNguoiDung, $thongTinNguoiDung->id_users); //tao tai khoan cho nguoi dung
                            $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoMa($thongTinNguoiDung->id_users);// cap nhat lai thong tin nguoi dung
                        }
                    }
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
                $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoMa($thongTinNguoiDung->id_users);// cap nhat lai thong tin nguoi dung
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
                if (isset($request->taoTaiKhoan)) {
                    if ($request->taoTaiKhoan == "on") {
                        $dataNguoiDung = [
                            NULL, //manguoidung tu tang
                            $request->hoTen,
                            $request->soDienThoai,
                            $request->diaChi,
                            1, //trangthai 0 la bi khoa, 1 la dang hoat dong
                            0, //loainguoidung 0 là khách hàng, 1 là đối tác, 2 là nhân viên
                            $request->email,
                            bcrypt($request->matKhau),
                            $ngayTao
                        ];
                    }
                }
                $this->nguoiDung->themNguoiDung($dataNguoiDung); //them nguoi dung vao database
                $thongTinNguoiDung = $this->nguoiDung->timNguoiDungTheoNgayTao($ngayTao); //tim nguoi dung vua them
            }
            $congNo = - $request->tongTien;
            $maGiamGiaDuocApDung = NULL;
            if (!empty(session('maGiamGia'))) {
                $thongTinMaGiamGia = $this->maGiamGia->timMaGiamGiaTheoMa(session('maGiamGia')->id_discount); //tim ma giam gia
                if (!empty($thongTinMaGiamGia)) {
                    if (strtotime($thongTinMaGiamGia->end_date) - strtotime(date('Y-m-d')) >= 0) { //neu con han su dung
                        $maGiamGiaDuocApDung = $thongTinMaGiamGia->id_discount;
                        $congNo += $thongTinMaGiamGia->reduced_price;
                        if ($congNo > 0) $congNo = 0;
                        session()->forget('maGiamGia');
                    } else {
                        return back()->with('thongbao', 'Mã giảm giá đã hết hạn sử dụng!');
                    }
                } else {
                    return back()->with('thongbao', 'Mã giảm giá không tồn tại!');
                }
            }
            $dataPhieuXuat = [
                NULL, //maphieuxuat tu dong
                $thongTinNguoiDung->name_users,    // hotennguoinhan,
                $thongTinNguoiDung->phone,    // sodienthoainguoinhan,
                $thongTinNguoiDung->address,    // diachinguoinhan,
                $thongTinNguoiDung->id_users,
                $maGiamGiaDuocApDung,    // magiamgia,
                $request->ghiChu,
                $request->tongTien,
                1,    // tinhtranggiaohang,  	0 là đã hủy, 1 là chờ xác nhận, 2 là đang chuẩn bị hàng, 3 là đang giao, 4 là đã giao thành công
                $request->hinhThucThanhToan,    // hinhthucthanhtoan,   0 là tiền mặt, 1 là chuyển khoản, 2 là atm qua vpn
                $congNo,    // congno, 0 là đã thanh toán, !=0 là công nợ
                $ngayTao    // ngaytao
            ];
            if (isset($request->thongTinNguoiNhanKhac)) {
                if ($request->thongTinNguoiNhanKhac == "on") {
                    $dataPhieuXuat = [
                        NULL, //maphieuxuat tu dong
                        $request->hoTenNguoiNhan,    // hotennguoinhan,
                        $request->soDienThoaiNguoiNhan,    // sodienthoainguoinhan,
                        $request->diaChiNguoiNhan,    // diachinguoinhan,
                        $thongTinNguoiDung->id_users,
                        $maGiamGiaDuocApDung,    // magiamgia,
                        $request->ghiChu,
                        $request->tongTien,
                        1,    // tinhtranggiaohang,  	0 là đã hủy, 1 là chờ xác nhận, 2 là đang chuẩn bị hàng, 3 là đang giao, 4 là đã giao thành công
                        $request->hinhThucThanhToan,    // hinhthucthanhtoan,   0 là tiền mặt, 1 là chuyển khoản, 2 là atm qua vpn
                        $congNo,    // congno, 0 là đã thanh toán, !=0 là công nợ
                        $ngayTao    // ngaytao
                    ];
                }
            }
            //https://sandbox.vnpayment.vn/apis/docs/huong-dan-tich-hop/
            if ($request->hinhThucThanhToan == 2) {
                $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
                $vnp_Returnurl = url('thanhtoan') . "";
                $vnp_TmnCode = "M0BKWT0Y"; //Mã website tại VNPAY
                $vnp_HashSecret = "JNDBAWSROHDXUQVKHHQZOXQZBHYXNXTI"; //Chuỗi bí mật
                $vnp_TxnRef = time() . ""; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
                $vnp_OrderInfo = json_encode($dataPhieuXuat) . "";
                $vnp_OrderType = 'billpayment';
                $vnp_Amount = (- $congNo) * 100;
                $vnp_Locale = 'vn';
                $vnp_BankCode = 'NCB';
                $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
                // dd($vnp_OrderInfo);
                $inputData = array(
                    "vnp_Version" => "2.0.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef
                );
                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }
                if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
                    $inputData['vnp_Bill_State'] = $vnp_Bill_State;
                }
                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    // if ($i == 1) {
                    //     $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    // } else {
                    //     $hashdata .= urlencode($key) . "=" . urlencode($value);
                    //     $i = 1;
                    // }
                    if ($i == 1) {
                        $hashdata .= '&' . $key . "=" . $value;
                    } else {
                        $hashdata .= $key . "=" . $value;
                        $i        = 1;
                    }
                   
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }
                $vnp_Url = $vnp_Url . "?" . $query;
                if (isset($vnp_HashSecret)) {
                    $vnpSecureHash = hash('sha256', $vnp_HashSecret . $hashdata); //
                    $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
                }
                $returnData = array(
                    'code' => '00', 'message' => 'success', 'data' => $vnp_Url
                );
                if ($request->thaoTac == "đặt hàng") {
                    return redirect()->to($vnp_Url);
                } else {
                    echo json_encode($returnData);
                }
            }
            $this->phieuXuat->themPhieuXuat($dataPhieuXuat); //them phieu xuat vao database
            $thongTinPhieuXuat = $this->phieuXuat->timPhieuXuatTheoNgayTao($ngayTao); //tim phieu xuat vua them
            foreach (session('gioHang') as $ctgh) {
                $donGia = $ctgh['sale_price'];
                if (!empty($ctgh['promotional_price'])) {
                    $donGia = $ctgh['promotional_price'];
                }
                if (!empty($ctgh['gift'])) { // xem chi tiet gio hang san pham do co qua tang khong neu co qua tang xuat them chi tiet phieu xuat 0 dong
                    foreach ($ctgh['gift'] as $thongTinSanPham) {
                        $dataChiTietPhieuXuat = [
                            NULL, //machitietphieuxuat  tu dong
                            $thongTinPhieuXuat->id_invoice,
                            $thongTinSanPham->id_products,
                            $thongTinSanPham->guarantee,
                            $ctgh['soluongmua'], //so luong qua tang theo so luong mua cua san pham
                            0 //don gia qua tang la 0 dong
                        ];
                        $this->chiTietPhieuXuat->themChiTietPhieuXuat($dataChiTietPhieuXuat); //them chi tiet phieu xuat vao database
                    }
                }
                $dataChiTietPhieuXuat = [
                    NULL, //machitietphieuxuat  tu dong
                    $thongTinPhieuXuat->id_invoice,
                    $ctgh['id_products'],
                    $ctgh['guarantee'],
                    $ctgh['soluongmua'],
                    $donGia
                ];
                $this->chiTietPhieuXuat->themChiTietPhieuXuat($dataChiTietPhieuXuat); //them chi tiet phieu xuat vao database
            }
            session()->forget('gioHang');
            return redirect()->route('/')->with('thongbao', 'Đặt hàng thành công, sẽ có nhân viên liên hệ bạn để xác nhận trong 24h tới!');
        }
        return redirect()->route('/')->with('thongbao', 'Thao tác thất bại vui lòng thử lại!');
    }
}
