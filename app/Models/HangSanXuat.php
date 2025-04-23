<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class HangSanXuat extends Model
{
    use HasFactory;
    public function layDanhSachHangSanXuat(){
        $danhSachHangSanXuat = DB::select('SELECT * FROM manufacturer ORDER BY id_mfg DESC');
        return $danhSachHangSanXuat;
    }
    public function timHangSanXuatTheoMa($mahang){
        $hangSanXuat = DB::select('SELECT * FROM manufacturer WHERE id_mfg = ?',[$mahang]);
        if(!empty($hangSanXuat)){
            return $hangSanXuat[0];
        }
        return $hangSanXuat;
    }
    public function timHangSanXuatTheoTen($tenhang){
        $hangSanXuat = DB::select('SELECT * FROM manufacturer WHERE name_mfg = ?',[$tenhang]);
        if(!empty($hangSanXuat)){
            return $hangSanXuat[0];
        }
        return $hangSanXuat;
    }
    public function xoaHangSanXuat($mahang){
        return DB::select('DELETE FROM manufacturer WHERE id_mfg = ?',[$mahang]);
    }
    public function themHangSanXuat($data){
        return DB::insert('INSERT INTO manufacturer (
            id_mfg,
            name_mfg,
            cat_mfg) values (
            ?,
            ?,
            ?)', $data);
    }
}
