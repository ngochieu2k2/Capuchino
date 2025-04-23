<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PhieuNhap extends Model
{
    use HasFactory;
    public function layDanhSachPhieuNhap(){
        $danhSachPhieuNhap = DB::select('SELECT * FROM purchase_order ORDER BY id_purchase_order ASC');
        return $danhSachPhieuNhap;
    }
    public function timPhieuNhapTheoMa($maphieunhap){
        $phieuNhap = DB::select('SELECT * FROM purchase_order WHERE id_purchase_order = ?',[$maphieunhap]);
        if(!empty($phieuNhap)){
            return $phieuNhap[0];
        }
        return $phieuNhap;
    }
    public function timPhieuNhapTheoNgayTao($ngaytao){
        $phieuNhap = DB::select('SELECT * FROM purchase_order WHERE date_created = ?',[$ngaytao]);
        if(!empty($phieuNhap)){
            return $phieuNhap[0];
        }
        return $phieuNhap;
    }
    public function xoaPhieuNhap($maphieunhap){
        return DB::select('DELETE FROM purchase_order WHERE id_purchase_order = ?',[$maphieunhap]);
    }
    public function suaPhieuNhap($data,$maphieunhap){
        $data = array_merge($data,[$maphieunhap]);
        return DB::select('UPDATE purchase_order SET
            note = ?,
            total_money = ?,
            debt = ?
            WHERE id_purchase_order = ?',$data);
    }
    public function themPhieuNhap($data){
        return DB::insert('INSERT INTO purchase_order (
            id_purchase_order,
            id_users,
            note,
            total_money,
            debt,
            date_created) values (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?)', $data);
    }
}
