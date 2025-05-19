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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        $filter = $request->input('filter', 'week');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $day = $request->input('day', now()->toDateString());

        // Hàm tiện ích lấy ngày theo filter (chỉ dùng date_created)
        $dateCondition = function ($query, $from, $to = null) {
            if ($to) {
                $query->whereBetween(DB::raw("DATE(date_created)"), [$from, $to]);
            } else {
                $query->whereDate('date_created', $from);
            }
        };

        $labels = [];
        $ordersData = [];
        $revenueData = [];
        $from = $to = null;

        if ($filter == 'day') {
            $from = $to = $day;
            $labels[] = $day;
        } elseif ($filter == 'week') {
            $from = now()->startOfWeek()->toDateString();
            $to = now()->endOfWeek()->toDateString();
            $period = \Carbon\CarbonPeriod::create($from, $to);
            foreach ($period as $date) {
                $labels[] = $date->toDateString();
            }
        } elseif ($filter == 'month') {
            $from = "$year-01-01";
            $to = "$year-12-31";
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = "Tháng $m";
            }
        } elseif ($filter == 'year') {
            $currentYear = now()->year;
            $from = ($currentYear - 4) . "-01-01";
            $to = $currentYear . "-12-31";
            for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
                $labels[] = "Năm $y";
            }
        }

        // Tổng sản phẩm (KHÔNG áp dụng filter)
        $tongSanPham = DB::table('products')->count();

        // Thống kê sản phẩm theo danh mục (KHÔNG áp dụng filter)
        $sanPhamTheoDanhMuc = DB::table('products')
            ->select('cat_products', DB::raw('COUNT(*) as so_luong'))
            ->groupBy('cat_products')
            ->pluck('so_luong', 'cat_products');

        // Tổng đơn hàng
        $tongDonHang = DB::table('invoice')
            ->when($filter == 'day', function ($q) use ($dateCondition, $from) {
                $dateCondition($q, $from);
            })
            ->when($filter == 'week' || $filter == 'month' || $filter == 'year', function ($q) use ($dateCondition, $from, $to) {
                $dateCondition($q, $from, $to);
            })
            ->count();

        // Tổng doanh thu
        $tongDoanhThu = DB::table('invoice')
            ->when($filter == 'day', function ($q) use ($dateCondition, $from) {
                $dateCondition($q, $from);
            })
            ->when($filter == 'week' || $filter == 'month' || $filter == 'year', function ($q) use ($dateCondition, $from, $to) {
                $dateCondition($q, $from, $to);
            })
            ->sum('total_money');

        // Tổng khách hàng
        $tongKhachHang = DB::table('users')
            ->where(function ($q) use ($filter, $from, $to) {
                $q->where(function ($q1) use ($filter, $from, $to) {
                    // created_at hợp lệ
                    $q1->whereNotNull('created_at')
                        ->whereDate('created_at', '!=', '1970-01-01')
                        ->whereDate('created_at', '!=', '0000-00-00');
                    if ($filter == 'day') {
                        $q1->whereDate('created_at', $from);
                    } else {
                        $q1->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
                    }
                })
                    ->orWhere(function ($q2) use ($filter, $from, $to) {
                        // created_at null hoặc không hợp lệ, fallback date_created
                        $q2->where(function ($q3) {
                            $q3->whereNull('created_at')
                                ->orWhereDate('created_at', '1970-01-01')
                                ->orWhereDate('created_at', '0000-00-00');
                        });
                        if ($filter == 'day') {
                            $q2->whereDate('date_created', $from);
                        } else {
                            $q2->whereBetween(DB::raw('DATE(date_created)'), [$from, $to]);
                        }
                    });
            })
            ->count();

        // Tỷ lệ đơn hàng theo trạng thái
        $trangThaiDon = [
            'Chờ xác nhận' => DB::table('invoice')->when($filter == 'day', function ($q) use ($dateCondition, $from) {
                $dateCondition($q, $from);
            })->when($filter == 'week' || $filter == 'month' || $filter == 'year', function ($q) use ($dateCondition, $from, $to) {
                $dateCondition($q, $from, $to);
            })->where('delivery_status', 1)->count(),
            'Đang giao' => DB::table('invoice')->when($filter == 'day', function ($q) use ($dateCondition, $from) {
                $dateCondition($q, $from);
            })->when($filter == 'week' || $filter == 'month' || $filter == 'year', function ($q) use ($dateCondition, $from, $to) {
                $dateCondition($q, $from, $to);
            })->where('delivery_status', 3)->count(),
            'Hoàn thành' => DB::table('invoice')->when($filter == 'day', function ($q) use ($dateCondition, $from) {
                $dateCondition($q, $from);
            })->when($filter == 'week' || $filter == 'month' || $filter == 'year', function ($q) use ($dateCondition, $from, $to) {
                $dateCondition($q, $from, $to);
            })->where('delivery_status', 4)->count(),
            'Đã huỷ' => DB::table('invoice')->when($filter == 'day', function ($q) use ($dateCondition, $from) {
                $dateCondition($q, $from);
            })->when($filter == 'week' || $filter == 'month' || $filter == 'year', function ($q) use ($dateCondition, $from, $to) {
                $dateCondition($q, $from, $to);
            })->where('delivery_status', 0)->count(),
        ];
        $tyLeHoanThanh = $tongDonHang > 0 ? round(($trangThaiDon['Hoàn thành'] / $tongDonHang) * 100, 2) : 0;

        // Biểu đồ số lượng đơn hàng & doanh thu
        if ($filter == 'day') {
            $ordersData[] = $tongDonHang;
            $revenueData[] = $tongDoanhThu;
        } elseif ($filter == 'week') {
            foreach ($labels as $date) {
                $ordersData[] = DB::table('invoice')->whereDate('date_created', $date)->count();
                $revenueData[] = DB::table('invoice')->whereDate('date_created', $date)->sum('total_money');
            }
        } elseif ($filter == 'month') {
            for ($m = 1; $m <= 12; $m++) {
                $ordersData[] = DB::table('invoice')
                    ->whereYear('date_created', $year)
                    ->whereMonth('date_created', $m)
                    ->count();
                $revenueData[] = DB::table('invoice')
                    ->whereYear('date_created', $year)
                    ->whereMonth('date_created', $m)
                    ->sum('total_money');
            }
        } elseif ($filter == 'year') {
            $currentYear = now()->year;
            for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
                $ordersData[] = DB::table('invoice')
                    ->whereYear('date_created', $y)
                    ->count();
                $revenueData[] = DB::table('invoice')
                    ->whereYear('date_created', $y)
                    ->sum('total_money');
            }
        }

        // Top 5 sản phẩm bán chạy
        $topSanPhamBanChay = DB::table('invoice_details')
            ->join('invoice', 'invoice.id_invoice', '=', 'invoice_details.id_invoice')
            ->when($filter == 'day', function ($q) use ($dateCondition, $from) {
                $dateCondition($q, $from);
            })
            ->when($filter == 'week' || $filter == 'month' || $filter == 'year', function ($q) use ($dateCondition, $from, $to) {
                $dateCondition($q, $from, $to);
            })
            ->select('invoice_details.id_products', DB::raw('SUM(invoice_details.qty) as so_luong'))
            ->groupBy('invoice_details.id_products')
            ->orderByDesc('so_luong')
            ->limit(5)
            ->get();

        // Top 5 khách hàng mua nhiều nhất
        $topKhachHang = DB::table('invoice')
            ->when($filter == 'day', function ($q) use ($dateCondition, $from) {
                $dateCondition($q, $from);
            })
            ->when($filter == 'week' || $filter == 'month' || $filter == 'year', function ($q) use ($dateCondition, $from, $to) {
                $dateCondition($q, $from, $to);
            })
            ->select('id_users', DB::raw('COUNT(*) as so_don'), DB::raw('SUM(total_money) as tong_tien'))
            ->groupBy('id_users')
            ->orderByDesc('tong_tien')
            ->limit(5)
            ->get();

        return view('admin.tongquan', compact(
            'labels',
            'ordersData',
            'revenueData',
            'topSanPhamBanChay',
            'topKhachHang',
            'tongSanPham',
            'sanPhamTheoDanhMuc',
            'tongDonHang',
            'tongDoanhThu',
            'tongKhachHang',
            'trangThaiDon',
            'tyLeHoanThanh',
            'filter',
            'year',
            'month',
            'day'
        ));
    }
}
