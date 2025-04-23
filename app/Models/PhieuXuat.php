<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PhieuXuat extends Model
{
    use HasFactory;
    protected $table = 'invoice';
    public function layDanhSachPhieuXuat()
    {
        $danhSachPhieuXuat = DB::select('SELECT * FROM invoice ORDER BY id_invoice DESC');
        return $danhSachPhieuXuat;
    }
    public function layDanhSachPhieuXuatTheoBoLoc($boLoc = [])
    {
        $danhSachPhieuXuat = DB::table($this->table);
        if (!empty($boLoc)) {
            foreach ($boLoc as $bl) {
                if (count($bl) == 2) {
                    $danhSachPhieuXuat = $danhSachPhieuXuat->whereIn($bl[0], $bl[1]);
                } else if (count($bl) == 3) {
                    $danhSachPhieuXuat = $danhSachPhieuXuat->where([$bl]);
                }
            }
        }
        $danhSachPhieuXuat = $danhSachPhieuXuat->orderby($this->table . '.date_created', 'DESC');
        $danhSachPhieuXuat = $danhSachPhieuXuat->get()->all();
        return $danhSachPhieuXuat;
    }
    public function timPhieuXuatTheoMa($maphieuxuat)
    {
        $phieuXuat = DB::select('SELECT * FROM invoice WHERE id_invoice = ?', [$maphieuxuat]);
        if (!empty($phieuXuat)) {
            return $phieuXuat[0];
        }
        return $phieuXuat;
    }
    public function timPhieuXuatTheoNgayTao($ngaytao)
    {
        $phieuXuat = DB::select('SELECT * FROM invoice WHERE date_created = ?', [$ngaytao]);
        if (!empty($phieuXuat)) {
            return $phieuXuat[0];
        }
        return $phieuXuat;
    }
    public function doiTinhTrangGiaoHangPhieuXuat($data,$maphieuxuat){
        $data = array_merge($data,[$maphieuxuat]);
        return DB::select('UPDATE invoice SET
            delivery_status = ?
            WHERE id_invoice = ?',$data);
    }
    public function doanhThuTuanNay(){
        $data=[
            now()->startOfWeek()->format('d').'',
            now()->endOfWeek()->format('d').''
        ];
        $doanhThu7Ngay=[
            [$data[0]+0],
            [$data[0]+1],
            [$data[0]+2],
            [$data[0]+3],
            [$data[0]+4],
            [$data[0]+5],
            [$data[0]+6],
        ];
        $doanhThu=DB::select('SELECT DAY(`date_created`) AS ngay, SUM(`total_money`) AS doanhthu FROM invoice WHERE delivery_status = 4 AND DAY(`date_created`) BETWEEN ? AND ? GROUP BY ngay',$data);
        if(!empty($doanhThu)){
            $data=[];
            foreach($doanhThu7Ngay as $dt7n){
                $flag=false;
                foreach($doanhThu as $dt){
                    if($dt7n[0] . "" == $dt->ngay . ""){
                        $data = array_merge($data, [$dt]);
                        $flag=true;
                        break;
                    }
                }
                if(!$flag){
                    $data = array_merge($data, [0]);
                }
            }
            return $data;
        }
        $doanhThu7Ngay=[
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
    public function suaPhieuXuat($data,$maphieuxuat){
        $data = array_merge($data,[$maphieuxuat]);
        return DB::select('UPDATE invoice SET
            name_receiver = ?,
            phone_receiver = ?,
            address_receiver = ?,
            note = ?,
            total_money = ?,
            delivery_status = ?,
            payments = ?,
            debt = ?
            WHERE id_invoice = ?',$data);
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
}
