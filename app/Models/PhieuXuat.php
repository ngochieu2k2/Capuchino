<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PhieuXuat extends Model
{
    use HasFactory;
    protected $table = 'invoice';
    protected $primaryKey = 'id_invoice';
    public $timestamps = false;
    public function layDanhSachPhieuXuat()
    {
        $danhSachPhieuXuat = DB::select('SELECT * FROM invoice ORDER BY id_invoice DESC');
        return $danhSachPhieuXuat;
    }
    public function layDanhSachPhieuXuatTheoBoLoc($boLoc = [])
    {
        $query = $this->newQuery();
        if (!empty($boLoc)) {
            foreach ($boLoc as $item) {
                $query->where($item[0], $item[1], $item[2]);
            }
        }
        return $query->orderByDesc('id_invoice')->get();
    }
    public function timPhieuXuatTheoMa($maphieuxuat)
    {
        return $this->where('id_invoice', $maphieuxuat)->first();
    }

    public function timPhieuXuatTheoNgayTao($ngaytao)
    {
        return $this->where('date_created', $ngaytao)->first();
    }
    public function doiTinhTrangGiaoHangPhieuXuat($data, $maphieuxuat)
    {
        $data = array_merge($data, [$maphieuxuat]);
        return DB::select('UPDATE invoice SET
            delivery_status = ?
            WHERE id_invoice = ?', $data);
    }
    public function doanhThuTuanNay()
    {
        $data = [
            now()->startOfWeek()->format('d') . '',
            now()->endOfWeek()->format('d') . ''
        ];
        $doanhThu7Ngay = [
            [$data[0] + 0],
            [$data[0] + 1],
            [$data[0] + 2],
            [$data[0] + 3],
            [$data[0] + 4],
            [$data[0] + 5],
            [$data[0] + 6],
        ];
        $doanhThu = DB::select('SELECT DAY(`date_created`) AS ngay, SUM(`total_money`) AS doanhthu FROM invoice WHERE delivery_status = 4 AND DAY(`date_created`) BETWEEN ? AND ? GROUP BY ngay', $data);
        if (!empty($doanhThu)) {
            $data = [];
            foreach ($doanhThu7Ngay as $dt7n) {
                $flag = false;
                foreach ($doanhThu as $dt) {
                    if ($dt7n[0] . "" == $dt->ngay . "") {
                        $data = array_merge($data, [$dt]);
                        $flag = true;
                        break;
                    }
                }
                if (!$flag) {
                    $data = array_merge($data, [0]);
                }
            }
            return $data;
        }
        $doanhThu7Ngay = [
            ['doanhthu' => 0],
            ['doanhthu' => 0],
            ['doanhthu' => 0],
            ['doanhthu' => 0],
            ['doanhthu' => 0],
            ['doanhthu' => 0],
            ['doanhthu' => 0],
        ];
        return $doanhThu7Ngay;
    }
    public function suaPhieuXuat($data, $maphieuxuat)
    {
        $data = array_merge($data, [$maphieuxuat]);
        return DB::select('UPDATE invoice SET
            name_receiver = ?,
            phone_receiver = ?,
            address_receiver = ?,
            note = ?,
            total_money = ?,
            delivery_status = ?,
            payments = ?,
            debt = ?
            WHERE id_invoice = ?', $data);
    }
    public function xoaPhieuXuat($maphieuxuat)
    {
        return DB::select('DELETE FROM invoice WHERE id_invoice = ?', [$maphieuxuat]);
    }
    public function themPhieuXuat($data)
    {
        return DB::insert('INSERT INTO invoice (
            id_invoice,
            name_receiver,
            phone_receiver,
            address_receiver,
            id_users,
            id_discount,
            note,
            total_money,
            delivery_status,
            payments,
            debt,
            date_created) values (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?)', $data);
    }
    public function phieuXuatTheoId($id)
    {
        $phieuXuat = DB::select('SELECT * FROM invoice WHERE id_invoice = ?', [$id]);
        if (!empty($phieuXuat)) {
            return (object)$phieuXuat[0];
        }
        return null;
    }
}
