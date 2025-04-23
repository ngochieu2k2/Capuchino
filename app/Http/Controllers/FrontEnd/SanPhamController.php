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

class SanPhamController extends Controller
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
    public function danhsachsp(Request $request)
    {
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        $danhSachLaptop = $this->laptop->layDanhSachLaptop();
        $danhSachThuVienHinh = $this->thuVienHinh->layDanhSachThuVienHinh();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        if (!empty($request->all())) {
            $boLoc = [];
            $Cpu = [];
            $Ram = [];
            $cardDoHoa = [];
            $oCung = [];
            $manHinh = [];
            $nhuCau = [];
            $tinhTrang = [];
            $mucGia = [];
            $tuKhoa = NULL;
            $sapXep = NULL;
            if (isset($request->loaisp) && $request->loaisp == 0) {
                $boLoc[] = ['products.cat_products', '=', 0];
            }
            if (!empty($request->hangsx)) {
                $hangsx = explode(',', $request->hangsx);
                $boLoc[] = ['products.id_mfg', $hangsx];
            }
            if (!empty($request->cpu)) {
                $cpu = explode(',', $request->cpu);
                if (in_array('intelcorei3', $cpu)) {
                    $Cpu[] = ['laptop.cpu', 'like', '%Intel Core i3%'];
                }
                if (in_array('intelcorei5', $cpu)) {
                    $Cpu[] = ['laptop.cpu', 'like', '%Intel Core i5%'];
                }
                if (in_array('intelcorei7', $cpu)) {
                    $Cpu[] = ['laptop.cpu', 'like', '%Intel Core i7%'];
                }
                if (in_array('amdryzen3', $cpu)) {
                    $Cpu[] = ['laptop.cpu', 'like', '%Amd Ryzen 3%'];
                }
                if (in_array('amdryzen5', $cpu)) {
                    $Cpu[] = ['laptop.cpu', 'like', '%Amd Ryzen 5%'];
                }
                if (in_array('amdryzen7', $cpu)) {
                    $Cpu[] = ['laptop.cpu', 'like', '%Amd Ryzen 7%'];
                }
            }
            if (!empty($request->ram)) {
                $ram = explode(',', $request->ram);
                if (in_array(4, $ram)) {
                    $Ram[] = ['laptop.ram', '=', 4];
                }
                if (in_array(8, $ram)) {
                    $Ram[] = ['laptop.ram', '=', 8];
                }
                if (in_array(16, $ram)) {
                    $Ram[] = ['laptop.ram', '=', 16];
                }
            }
            if (!empty($request->carddohoa)) {
                $carddohoa = explode(',', $request->carddohoa);
                if (in_array('onboard', $carddohoa)) {
                    $cardDoHoa[] = ['laptop.card_laptop', '=', 0];
                }
                if (in_array('nvidia', $carddohoa)) {
                    $cardDoHoa[] = ['laptop.card_laptop', '=', 1];
                }
                if (in_array('amd', $carddohoa)) {
                    $cardDoHoa[] = ['laptop.card_laptop', '=', 2];
                }
            }
            if (!empty($request->ocung)) {
                $ocung = explode(',', $request->ocung);
                if (in_array(128, $ocung)) {
                    $oCung[] = ['laptop.disk_laptop', '=', 128];
                }
                if (in_array(256, $ocung)) {
                    $oCung[] = ['laptop.disk_laptop', '=', 256];
                }
                if (in_array(512, $ocung)) {
                    $oCung[] = ['laptop.disk_laptop', '=', 512];
                }
            }
            if (!empty($request->manhinh)) {
                $manhinh = explode(',', $request->manhinh);
                if (in_array(13, $manhinh)) {
                    $manHinh[] = [13, 13.9];
                }
                if (in_array(14, $manhinh)) {
                    $manHinh[] = [14, 14.9];
                }
                if (in_array(15, $manhinh)) {
                    $manHinh[] = [15, 16];
                }
            }
            if (!empty($request->nhucau)) {
                $nhucau = explode(',', $request->nhucau);
                if (in_array('sinhvien', $nhucau)) {
                    $nhuCau[] = ['laptop.demand', '=', 'Sinh Viên'];
                }
                if (in_array('dohoa', $nhucau)) {
                    $nhuCau[] = ['laptop.demand', '=', 'Đồ Họa'];
                }
                if (in_array('gaming', $nhucau)) {
                    $nhuCau[] = ['laptop.demand', '=', 'Gaming'];
                }
            }
            if (!empty($request->tinhtrang)) {
                $tinhtrang = explode(',', $request->tinhtrang);
                if (in_array('moi', $tinhtrang)) {
                    $tinhTrang[] = ['laptop.status', '=', 0];
                }
                if (in_array('cu', $tinhtrang)) {
                    $tinhTrang[] = ['laptop.status', '=', 1];
                }
            }
            if (!empty($request->mucgia)) {
                $mucgia = explode(',', $request->mucgia);
                if (in_array('duoi10', $mucgia)) {
                    $mucGia[] = [0, 10000000];
                }
                if (in_array('1015', $mucgia)) {
                    $mucGia[] = [10000000, 15000000];
                }
                if (in_array('1520', $mucgia)) {
                    $mucGia[] = [15000000, 20000000];
                }
                if (in_array('tren20', $mucgia)) {
                    $mucGia[] = [20000000, 2000000000];
                }
            }
            if (!empty($request->sapxep)) {
                $sapXep = $request->sapxep;
            }
            // $boLoc = [], $tuKhoa = NULL, $sapXep = NULL, $mucGia = [], $tinhTrang = [], $nhuCau = [], $manHinh = [], $oCung = [], $cardDoHoa = [], $Ram = [], $Cpu = []
            $danhSachSanPham = $this->sanPham->layDanhSachSanPhamTheoBoLoc($boLoc, $tuKhoa, $sapXep, $mucGia, $tinhTrang, $nhuCau, $manHinh, $oCung, $cardDoHoa, $Ram, $Cpu);
        }
        return view('user.danhsachsp', compact(
            'danhSachSanPham',
            'danhSachLaptop',
            'danhSachThuVienHinh',
            'danhSachHangSanXuat'
        ));
    }
    public function chitietsp(Request $request)
    {
        $request->validate(['masp' => 'required|integer|exists:products,id_products']);
        $sanPhamXem = $this->sanPham->timSanPhamTheoMa($request->masp);
        $cauHinh = NULL;
        $thongTinPhuKien = NULL;
        if ($sanPhamXem->cat_products == 0 && !empty($sanPhamXem->id_laptop)) { //la laptop
            $cauHinh = $this->laptop->timLaptopTheoMa($sanPhamXem->id_laptop);
        } elseif ($sanPhamXem->cat_products == 1 && !empty($sanPhamXem->id_accessory)) { //la phu kien
            $thongTinPhuKien = $this->phuKien->timPhuKienTheoMa($sanPhamXem->id_accessory);
        }
        $thuVienHinhXem = $this->thuVienHinh->timThuVienHinhTheoMa($sanPhamXem->id_photo);
        $hangSanXuatXem = $this->hangSanXuat->timHangSanXuatTheoMa($sanPhamXem->id_mfg);
        $quaTangXem = $this->quaTang->timQuaTangTheoMa($sanPhamXem->id_gift);
        $danhSachSanPham = $this->sanPham->layDanhSachSanPham();
        $danhSachThuVienHinh = $this->thuVienHinh->layDanhSachThuVienHinh();
        $danhSachLaptop = $this->laptop->layDanhSachLaptop();
        $danhSachSanPhamTang = [];
        $flag = false;
        foreach ($quaTangXem as $giaTri) {
            if ($flag && !empty($giaTri)) {
                $sanPhamTang = $this->sanPham->timSanPhamTheoMa($giaTri);
                if (!empty($sanPhamTang)) {
                    $danhSachSanPhamTang = array_merge($danhSachSanPhamTang, [$sanPhamTang]);
                }
            }
            if (is_string($giaTri)) $flag = true;
        }
        $danhSachSanPhamTuongTu = [];
        $danhSachLaptopCu = [];
        foreach ($danhSachSanPham as $sanpham) {
            if ($sanpham->cat_products == $sanPhamXem->cat_products && $sanpham->id_products != $sanPhamXem->id_products) {
                $danhSachSanPhamTuongTu = array_merge($danhSachSanPhamTuongTu, [$sanpham]);
            }
            if ($sanpham->cat_products == 0 && $sanpham->id_products != $sanPhamXem->id_products) {
                $thongTinLaptop = $this->laptop->timLaptopTheoMa($sanpham->id_laptop);
                if ($thongTinLaptop->status == 1) { // la laptop cu
                    $danhSachLaptopCu = array_merge($danhSachLaptopCu, [$sanpham]);
                }
            }
        }
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        return view('user.chitietsp', compact(
            'sanPhamXem',
            'cauHinh',
            'thongTinPhuKien',
            'thuVienHinhXem',
            'hangSanXuatXem',
            'danhSachHangSanXuat',
            'danhSachSanPhamTang',
            'danhSachSanPhamTuongTu',
            'danhSachThuVienHinh',
            'danhSachLaptopCu',
            'danhSachLaptop'
        ));
    }
    public function timkiem(Request $request)
    {
        $boLoc = [];
        $tuKhoa = NULL;
        $sapXep = NULL;
        if (!empty($request->boloc)) {
            if ($request->boloc == -1) { //laptop
                $boLoc[] = ['products.cat_products', '=', 0];
            } else if ($request->boloc == -2) { //phukien
                $boLoc[] = ['products.cat_products', '=', 1];
            } else if ($request->boloc != 0) { //mahang
                $boLoc[] = ['products.id_mfg', '=', $request->boloc];
            }
        }
        if (!empty($request->tukhoa)) {
            $tuKhoa = $request->tukhoa;
        }
        if (!empty($request->sapxep)) {
            $sapXep = $request->sapxep;
        }
        $danhSachSanPham = $this->sanPham->layDanhSachSanPhamTheoBoLoc($boLoc, $tuKhoa, $sapXep);
        $danhSachLaptop = $this->laptop->layDanhSachLaptop();
        $danhSachThuVienHinh = $this->thuVienHinh->layDanhSachThuVienHinh();
        $danhSachHangSanXuat = $this->hangSanXuat->layDanhSachHangSanXuat();
        return view('user.timkiem', compact(
            'danhSachSanPham',
            'danhSachLaptop',
            'danhSachThuVienHinh',
            'danhSachHangSanXuat'
        ));
    }
}
