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

class XulyLaptop extends Controller
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
    public function xulylaptop(Request $request)
    {
        $request->validate(['thaoTac' => 'required|string']);
        if ($request->thaoTac == "xóa laptop") { // *******************************************************************************************xoa laptop
            $rules = [
                'maSanPhamXoa' => 'required|integer|exists:products,id_products'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'integer' => ':attribute nhập sai'
            ];
            $attributes = [
                'maSanPhamXoa' => 'Mã sản phẩm'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($request->maSanPhamXoa); //tim san pham
            $thongTinChiTietPhieuNhap = $this->chiTietPhieuNhap->timDanhSachChiTietPhieuNhapTheoMaSanPham($thongTinSanPham->id_products);
            if (!empty($thongTinChiTietPhieuNhap)) {
                return back()->with(
                    'tieudethongbao',
                    'Thao tác thất bại'
                )->with(
                    'thongbao',
                    'Laptop đã tồn tại trong phiếu nhập [PN' . $thongTinChiTietPhieuNhap[0]->id_purchase_order . '] nên không thể xóa'
                )->with(
                    'loaithongbao',
                    'danger'
                );
            }
            $thongTinChiTietPhieuXuat = $this->chiTietPhieuXuat->timDanhSachChiTietPhieuXuatTheoMaSanPham($thongTinSanPham->id_products);
            if (!empty($thongTinChiTietPhieuXuat)) {
                return back()->with(
                    'tieudethongbao',
                    'Thao tác thất bại'
                )->with(
                    'thongbao',
                    'Laptop đã tồn tại trong phiếu xuất [PX' . $thongTinChiTietPhieuXuat[0]->id_invoice . '] nên không thể xóa'
                )->with(
                    'loaithongbao',
                    'danger'
                );
            }
            if (!empty($thongTinSanPham)) {
                $this->sanPham->xoaSanPham($thongTinSanPham->id_products); //xoa san pham tren database
                if ($thongTinSanPham->cat_products == 0 && !empty($thongTinSanPham->id_laptop)) {
                    $thongTinLaptop = $this->laptop->timLaptopTheoMa($thongTinSanPham->id_laptop); //tim laptop
                    if (!empty($thongTinLaptop)) {
                        $this->laptop->xoaLaptop($thongTinLaptop->id_laptop); //xoa laptop tren database
                    }
                }
                $thongTinHinh = $this->thuVienHinh->timThuVienHinhTheoMa($thongTinSanPham->id_photo ); //tim thu vien hinh
                if (!empty($thongTinHinh)) {
                    $this->thuVienHinh->xoaThuVienHinh($thongTinHinh->id_photo ); //xoa thu vien hinh tren database
                    foreach ($thongTinHinh as $giaTri) {
                        if (!empty($giaTri)) {
                            $duongDanHinhCanXoa = 'img/sanpham/' . $giaTri;
                            if (File::exists($duongDanHinhCanXoa)) {
                                File::delete($duongDanHinhCanXoa); //xoa thu vien hinh tren host sever
                            }
                        }
                    }
                }
                $thongTinQuaTang = $this->quaTang->timQuaTangTheoMa($thongTinSanPham->id_gift); //tim qua tang
                if (!empty($thongTinQuaTang)) {
                    $this->quaTang->xoaQuaTang($thongTinQuaTang->id_gift); //xoa qua tang tren database
                }
                return back()->with(
                    'tieudethongbao',
                    'Thao tác thành công'
                )->with(
                    'thongbao',
                    'Xóa laptop thành công'
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
                'Xóa laptop thất bại'
            )->with(
                'loaithongbao',
                'danger'
            );
        }
        if ($request->thaoTac == "sửa laptop") { // *******************************************************************************************sua laptop
            $rules = [
                'maSanPhamSua' => 'required|integer|exists:products,id_products',
                'tenSanPhamSua' => 'required|string|max:150|min:3',
                'baoHanhSua' => 'required|integer|between:1,48',
                'cpuSua' => 'required|string|max:50|min:3',
                'hangSanXuatSua' => 'required|integer|exists:manufacturer,id_mfg',
                'ramSua' => 'required|integer|between:4,32',
                'cardDoHoaSua' => 'required|integer|between:0,2',
                'oCungSua' => 'required|integer|between:128,512',
                'manHinhSua' => 'required|numeric|between:5,15',
                'nhuCauSua' => 'required|string|max:50|min:3',
                'tinhTrangSua' => 'required|boolean',
                'quaTangSua' => 'required|array|size:5',
                'hinhSanPhamSua' => 'array|between:1,5',
                'hinhSanPhamSua.*' => 'image|dimensions:min_width=500,min_height=450,max_width=500,max_height=450',
                'moTaSua' => 'max:1024'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute nhập sai',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối thiểu :max ký tự',
                'between' => ':attribute vượt quá số lượng cho phép',
                'size' => ':attribute không đúng số lượng (:size)',
                'exists' => ':attribute không tồn tại',
                'numeric' => ':attribute phải là ký tự số',
                'integer' => ':attribute nhập sai',
                'boolean' => ':attribute nhập sai',
                'array' => ':attribute nhập sai',
                'hinhSanPhamSua.array' => 'Hình sản phẩm chọn sai',
                'hinhSanPhamSua.between' => 'Hình sản phẩm vượt quá số lượng cho phép',
                'hinhSanPhamSua.*.image' => 'Hình sản phẩm không đúng định dạng',
                'hinhSanPhamSua.*.dimensions' => 'Hình sản phẩm không đúng kích thước :min_width x :min_height'
            ];
            $attributes = [
                'tenSanPhamSua' => 'Tên sản phẩm',
                'baoHanhSua' => 'Bảo hành',
                'cpuSua' => 'Cpu',
                'hangSanXuatSua' => 'Hãng sản xuất',
                'ramSua' => 'Ram',
                'cardDoHoaSua' => 'Card đồ họa',
                'oCungSua' => 'Ổ cứng',
                'manHinhSua' => 'Màn hình',
                'nhuCauSua' => 'Nhu cầu',
                'tinhTrangSua' => 'Tình trạng',
                'quaTangSua' => 'Qua tặng',
                'moTaSua' => 'Mô tả'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($request->maSanPhamSua); //tim san pham

            // ***********Xu ly sua san pham
            if ($thongTinSanPham->name_products != $request->tenSanPhamSua) { //so sanh ten san pham
                $sanPhamTrungTenSapDoi = $this->sanPham->timSanPhamTheoTen($request->tenSanPhamSua);
                if (empty($sanPhamTrungTenSapDoi)) { //ten sap doi khong bi trung
                    $thongTinSanPham->name_products = $request->tenSanPhamSua;
                } else {
                    return back()->with(
                        'tieudethongbao',
                        'Thao tác thất bại'
                    )->with(
                        'thongbao',
                        'Sửa thông tin laptop thất bại, tên sản phẩm đã tồn tại'
                    )->with(
                        'loaithongbao',
                        'danger'
                    );
                }
            }
            if ($thongTinSanPham->guarantee != $request->baoHanhSua) { //so sanh bao hanh
                $thongTinSanPham->guarantee = $request->baoHanhSua;
            }
            if ($thongTinSanPham->describes != $request->moTaSua) { //so sanh mo ta
                $thongTinSanPham->describes = $request->moTaSua;
            }
            if ($thongTinSanPham->id_mfg != $request->hangSanXuatSua) { //so sanh hang san xuat
                $thongTinSanPham->id_mfg = $request->hangSanXuatSua;
            }
            $dataSanPham = [
                $thongTinSanPham->name_products,
                $thongTinSanPham->guarantee,
                $thongTinSanPham->describes,
                $thongTinSanPham->id_mfg
            ];
            $this->sanPham->suaSanPham($dataSanPham, $thongTinSanPham->id_products); // sua thong tin san pham tren database
            // ***********Xu ly sua laptop
            if ($thongTinSanPham->cat_products == 0 && !empty($thongTinSanPham->id_laptop)) { // la laptop
                $dataLaptop = [
                    $thongTinSanPham->name_products,
                    $request->cpuSua,
                    (int)$request->ramSua,
                    (int)$request->cardDoHoaSua,
                    (int)$request->oCungSua,
                    (float)$request->manHinhSua,
                    $request->nhuCauSua,
                    (int)$request->tinhTrangSua
                ];
                $this->laptop->suaLaptop($dataLaptop, $thongTinSanPham->id_laptop); // sua thong tin laptop tren database
            }
            // ***********Xu ly them thu vien hinh (neu co)
            if (isset($request->hinhSanPhamSua)) {
                // ***********up hinh moi vao len host
                $tenHinh = [NULL, NULL, NULL, NULL, NULL];
                $dem = 0;
                if ($request->has('hinhSanPhamSua')) {
                    foreach ($request->hinhSanPhamSua as $hinh) {
                        $tenHinh[$dem] = $request->tenSanPhamSua . '-' . time() . '-' . $dem . '.' . $hinh->guessExtension();
                        $hinh->move(public_path('img/sanpham'), $tenHinh[$dem]);
                        $dem++;
                    }
                }
                // ***********xoa hinh cu tren host
                $thongTinHinh = $this->thuVienHinh->timThuVienHinhTheoMa($thongTinSanPham->id_photo ); //tim thu vien hinh
                if (!empty($thongTinHinh)) {
                    foreach ($thongTinHinh as $giaTri) {
                        if (!empty($giaTri)) {
                            $duongDanHinhCanXoa = 'img/sanpham/' . $giaTri;
                            if (File::exists($duongDanHinhCanXoa)) {
                                File::delete($duongDanHinhCanXoa); //xoa thu vien hinh tren host sever
                            }
                        }
                    }
                }
                // ***********sua thong tin thu vien hinh tren database
                $dataHinh = [
                    $thongTinSanPham->name_products,
                    $tenHinh[0], //hinh 1
                    $tenHinh[1], //hinh 2
                    $tenHinh[2], //hinh 3
                    $tenHinh[3], //hinh 4
                    $tenHinh[4], //hinh 5
                ];
                $this->thuVienHinh->suaThuVienHinh($dataHinh, $thongTinSanPham->id_photo ); //sua thong tin thu vien hinh tren database
            }
            // ***********Xu ly sua qua tang
            $dataQuaTang = [
                $thongTinSanPham->name_products, //ten san pham [0]
                NULL, //ma san pham 1 [1]
                NULL, //ma san pham 2 [2]
                NULL, //ma san pham 3 [3]
                NULL, //ma san pham 4 [4]
                NULL, //ma san pham 5 [5]
            ];
            $dem = 1;
            $quaTangSua = $request->quaTangSua;
            for ($i = 0; $i < count($quaTangSua); $i++) {
                if ($quaTangSua[$i] != NULL) {
                    for ($j = $i + 1; $j < count($quaTangSua); $j++) {
                        if ($quaTangSua[$i] == $quaTangSua[$j]) {
                            $quaTangSua[$j] = NULL;
                        }
                    }
                }
            }
            foreach ($quaTangSua as $maSanPhamQuaTang) {
                if (!empty($maSanPhamQuaTang)) {
                    $thongTinSanPhamTang = $this->sanPham->timSanPhamTheoMa($maSanPhamQuaTang);
                    if (!empty($thongTinSanPhamTang)) {
                        $dataQuaTang[$dem] = $thongTinSanPhamTang->id_products;
                        $dem++;
                    }
                }
            }
            $this->quaTang->suaQuaTang($dataQuaTang, $thongTinSanPham->id_gift); // sua thong tin qua tang tren database
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Sửa thông tin laptop thành công'
            )->with(
                'loaithongbao',
                'success'
            );
        }
        if ($request->thaoTac == "thêm laptop") { // *******************************************************************************************them laptop
            $rules = [
                'name_Products' => 'required|string|max:150|min:3|unique:products',
                'baoHanh' => 'required|integer|between:1,48',
                'cpu' => 'required|string|max:50|min:3',
                'hangSanXuat' => 'required|integer|exists:manufacturer,id_mfg',
                'ram' => 'required|integer|between:4,32',
                'cardDoHoa' => 'required|integer|between:0,2',
                'oCung' => 'required|integer|between:128,512',
                'manHinh' => 'required|numeric|between:10,30',
                'nhuCau' => 'required|string|max:50|min:3',
                'tinhTrang' => 'required|boolean',
                'quaTang' => 'required|array|size:5',
                'hinhSanPham' => 'required|array|between:1,5',
                'hinhSanPham.*' => 'image|dimensions:min_width=500,min_height=450,max_width=500,max_height=450',
                'moTa' => 'max:255'
            ];

            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'string' => ':attribute nhập sai',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'between' => ':attribute vượt quá số lượng cho phép',
                'size' => ':attribute không đúng số lượng (:size)',
                'unique' => ':attribute đã tồn tại',
                'exists' => ':attribute không tồn tại',
                'numeric' => ':attribute phải là ký tự số',
                'integer' => ':attribute nhập sai',
                'boolean' => ':attribute nhập sai',
                'array' => ':attribute nhập sai',
                'hinhSanPham.required' => 'Hình sản phẩm chọn sai',
                'hinhSanPham.array' => 'Hình sản phẩm chọn sai',
                'hinhSanPham.between' => 'Hình sản phẩm vượt quá số lượng cho phép',
                'hinhSanPham.*.image' => 'Hình sản phẩm không đúng định dạng',
                'hinhSanPham.*.dimensions' => 'Hình sản phẩm không đúng kích thước :min_width x :min_height'
            ];
            $attributes = [
                'name_Products' => 'Tên sản phẩm',
                'baoHanh' => 'Bảo hành',
                'cpu' => 'Cpu',
                'hangSanXuat' => 'Hãng sản xuất',
                'ram' => 'Ram',
                'cardDoHoa' => 'Card đồ họa',
                'oCung' => 'Ổ cứng',
                'manHinh' => 'Màn hình',
                'nhuCau' => 'Nhu cầu',
                'tinhTrang' => 'Tình trạng',
                'quaTang' => 'Qùa tặng',
                'moTa' => 'Mô tả'
            ];
            $request->validate($rules, $messages, $attributes);
            // ***********Xu ly them qua tang
            $dataQuaTang = [
                NULL, //ma qua tang [0]
                $request->name_Products, //ten san pham [1]
                NULL, //ma san pham 1 [2]
                NULL, //ma san pham 2 [4]
                NULL, //ma san pham 3 [5]
                NULL, //ma san pham 4 [6]
                NULL, //ma san pham 5 [7]
            ];
            $dem = 2;
            $quaTang = $request->quaTang;
            for ($i = 0; $i < count($quaTang); $i++) { // loc ma san pham tang bi trung
                if ($quaTang[$i] != NULL) {
                    for ($j = $i + 1; $j < count($quaTang); $j++) {
                        if ($quaTang[$i] == $quaTang[$j]) {
                            $quaTang[$j] = NULL;
                        }
                    }
                }
            }
            foreach ($quaTang as $maSanPhamQuaTang) {
                if (!empty($maSanPhamQuaTang)) {
                    $thongTinSanPhamTang = $this->sanPham->timSanPhamTheoMa($maSanPhamQuaTang);
                    if (!empty($thongTinSanPhamTang)) {
                        $dataQuaTang[$dem] = $thongTinSanPhamTang->id_products;
                        $dem++;
                    }
                }
            }

            // ***********Xu ly them thu vien hinh
            $tenHinh = [NULL, NULL, NULL, NULL, NULL];
            $dem = 0;
            if ($request->has('hinhSanPham')) {
                foreach ($request->hinhSanPham as $hinh) {
                    $tenHinh[$dem] = $request->name_Products . '-' . time() . '-' . $dem . '.' . $hinh->guessExtension();
                    $hinh->move(public_path('img/sanpham'), $tenHinh[$dem]);
                    $dem++;
                }
            }
            $dataHinh = [
                NULL, //ma hinh
                $request->name_Products,
                $tenHinh[0], //hinh 1
                $tenHinh[1], //hinh 2
                $tenHinh[2], //hinh 3
                $tenHinh[3], //hinh 4
                $tenHinh[4], //hinh 5
            ];
            // ***********Xu ly them laptop
            $dataLaptop = [
                NULL, //ma laptop
                $request->name_Products,
                $request->cpu,
                $request->ram,
                $request->cardDoHoa,
                $request->oCung,
                $request->manHinh,
                $request->nhuCau,
                $request->tinhTrang
            ];

            $this->quaTang->themQuaTang($dataQuaTang); //them vao database
            $thongTinQuaTang = $this->quaTang->timQuaTangTheoTenSanPham($request->name_Products); //tim qua tang vua them

            $this->thuVienHinh->themThuVienHinh($dataHinh); //them vao database
            $thongTinHinh = $this->thuVienHinh->timThuVienHinhTheoTenSanPham($request->name_Products); //tim thu vien hinh vua them

            $this->laptop->themLaptop($dataLaptop); //them vao database
            $thongTinLaptop = $this->laptop->timLaptopTheoTenSanPham($request->name_Products); //tim laptop vua them

            // ***********Xu ly them sanpham
            $dataSanPham = [
                NULL, //ma san pham
                $request->name_Products,
                $request->baoHanh,
                $request->moTa,
                0, //so luong
                0, //gia nhap
                0, //gia ban
                NULL, //gia khuyen mai
                $thongTinHinh->id_photo , //ma thu vien hinh
                $request->hangSanXuat, //ma hang
                $thongTinQuaTang->id_gift, //ma qua tang
                $thongTinLaptop->id_laptop, //ma lap top
                NULL, //ma phu kien
                0 //loai san pham
                //ngaytao tu dong
            ];
            $this->sanPham->themSanPham($dataSanPham);
            return back()->with(
                'tieudethongbao',
                'Thao tác thành công'
            )->with(
                'thongbao',
                'Thêm laptop mới thành công'
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
