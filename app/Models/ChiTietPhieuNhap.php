<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChiTietPhieuNhap extends Model
{
    use HasFactory;
    public function layDanhSachChiTietPhieuNhap()
    {
        $danhSachChiTietPhieuNhap = DB::select('SELECT * FROM purchase_order_details ORDER BY id_purchase_order_details DESC');
        return $danhSachChiTietPhieuNhap;
    }
    public function timDanhSachChiTietPhieuNhapTheoMaPhieuNhap($maphieunhap)
    {
        $danhSachChiTietPhieuNhap = DB::select('SELECT * FROM purchase_order_details WHERE id_purchase_order = ?', [$maphieunhap]);
        return $danhSachChiTietPhieuNhap;
    }
    public function timDanhSachChiTietPhieuNhapTheoMaSanPham($masanpham)
    {
        $danhSachChiTietPhieuNhap = DB::select('SELECT * FROM purchase_order_details WHERE id_products = ?', [$masanpham]);
        return $danhSachChiTietPhieuNhap;
    }
    public function xoaChiTietPhieuNhap($machitietphieunhap){
        return DB::select('DELETE FROM purchase_order_details WHERE id_purchase_order_details = ?',[$machitietphieunhap]);
    }
    public function themChiTietPhieuNhap($data)
    {
        return DB::insert('INSERT INTO purchase_order_details (
            id_purchase_order_details,
            id_purchase_order,
            id_products,
            qty,
            dongia) values (
            ?,
            ?,
            ?,
            ?,
            ?)', $data);
    }
}
