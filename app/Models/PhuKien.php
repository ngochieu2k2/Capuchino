<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PhuKien extends Model
{
    use HasFactory;
    public function layDanhSachPhuKien(){
        $danhSachPhuKien = DB::select('SELECT * FROM accessory ORDER BY id_accessory DESC');
        return $danhSachPhuKien;
    }
    public function timPhuKienTheoMa($maphukien){
        $phuKien = DB::select('SELECT * FROM accessory WHERE id_accessory = ?',[$maphukien]);
        if(!empty($phuKien)){
            return $phuKien[0];
        }
        return $phuKien;
    }
    public function timPhuKienTheoTenSanPham($tenSanPham){
        $phuKien = DB::select('SELECT * FROM accessory WHERE name_products = ?',[$tenSanPham]);
        if(!empty($phuKien)){
            return $phuKien[0];
        }
        return $phuKien;
    }
    public function xoaPhuKien($maphukien){
        return DB::select('DELETE FROM accessory WHERE id_accessory = ?',[$maphukien]);
    }
    public function suaPhuKien($data,$maphukien){
        $data = array_merge($data,[$maphukien]);
        return DB::select('UPDATE accessory SET
            name_products = ?,
            cat_accessory = ?
            WHERE id_accessory = ?',$data);
    }
    public function themPhuKien($data){
        return DB::insert('INSERT INTO accessory (
            id_accessory,
            name_products,
            cat_accessory) values (
            ?,
            ?,
            ?)', $data);
    }
}
