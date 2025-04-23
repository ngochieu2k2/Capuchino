<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ThuVienHinh extends Model
{
    use HasFactory;
    public function layDanhSachThuVienHinh(){
        $danhSachHinh = DB::select('SELECT * FROM photo ORDER BY id_photo DESC');
        return $danhSachHinh;
    }
    public function timThuVienHinhTheoMa($mathuvienhinh){
        $thuVienHinh = DB::select('SELECT * FROM photo WHERE id_photo = ?',[$mathuvienhinh]);
        if(!empty($thuVienHinh)){
            return $thuVienHinh[0];
        }
        return $thuVienHinh;
    }
    public function timThuVienHinhTheoTenSanPham($tenSanPham){
        $thuVienHinh = DB::select('SELECT * FROM photo WHERE name_products = ?',[$tenSanPham]);
        if(!empty($thuVienHinh)){
            return $thuVienHinh[0];
        }
        return $thuVienHinh;
    }
    public function xoaThuVienHinh($mathuvienhinh){
        return DB::select('DELETE FROM photo WHERE id_photo = ?',[$mathuvienhinh]);
    }
    public function suaThuVienHinh($data,$mathuvienhinh){
        $data = array_merge($data,[$mathuvienhinh]);

        return DB::select('UPDATE photo SET
            name_products = ?,
            photo_1 = ?,
            photo_2 = ?,
            photo_3 = ?,
            photo_4 = ?,
            photo_5 = ?
            WHERE id_photo = ?',$data);
    }
    public function themThuVienHinh($data){
        return DB::insert('INSERT INTO photo (
            id_photo,
            name_products,
            photo_1,
            photo_2,
            photo_3,
            photo_4,
            photo_5) values (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?)', $data);
    }
}
