<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChiTietPhieuXuat extends Model
{
    use HasFactory;
    public function layDanhSachChiTietPhieuXuat()
    {
        $danhSachChiTietPhieuXuat = DB::select('SELECT * FROM invoice_details ORDER BY id_invoice_details DESC');
        return $danhSachChiTietPhieuXuat;
    }
    public function timDanhSachChiTietPhieuXuatTheoMaPhieuXuat($maphieuxuat)
    {
        $danhSachChiTietPhieuXuat = DB::select('SELECT * FROM invoice_details WHERE id_invoice = ?', [$maphieuxuat]);
        return $danhSachChiTietPhieuXuat;
    }
    public function timDanhSachChiTietPhieuXuatTheoMaSanPham($masanpham)
    {
        $danhSachChiTietPhieuXuat = DB::select('SELECT * FROM invoice_details WHERE id_products = ?', [$masanpham]);
        return $danhSachChiTietPhieuXuat;
    }
    public function xoaChiTietPhieuXuat($machitietphieuxuat){
        return DB::select('DELETE FROM invoice_details WHERE id_invoice_details = ?',[$machitietphieuxuat]);
    }
    public function themChiTietPhieuXuat($data)
    {
        return DB::insert('INSERT INTO invoice_details (
            id_invoice_details,
            id_invoice,
            id_products,
            guarantee,
            qty,
            dongia) values (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?)', $data);
    }
}
