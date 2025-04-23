<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class QuaTang extends Model
{
    use HasFactory;
    public function layDanhSachQuaTang(){
        $danhSachQuaTang = DB::select('SELECT * FROM gift ORDER BY id_gift DESC');
        return $danhSachQuaTang;
    }
    public function timQuaTangTheoMa($maquatang){
        $quaTang = DB::select('SELECT * FROM gift WHERE id_gift = ?',[$maquatang]);
        if(!empty($quaTang)){
            return $quaTang[0];
        }
        return $quaTang;
    }
    public function timQuaTangTheoTenSanPham($tensanpham){
        $quaTang = DB::select('SELECT * FROM gift WHERE name_products = ?',[$tensanpham]);
        if(!empty($quaTang)){
            return $quaTang[0];
        }
        return $quaTang;
    }
    public function xoaQuaTang($maquatang){
        return DB::select('DELETE FROM gift WHERE id_gift = ?',[$maquatang]);
    }
    public function suaQuaTang($data,$maquatang){
        $data = array_merge($data,[$maquatang]);
        return DB::select('UPDATE gift SET
            name_products = ?,
            id_products_1 = ?,
            id_products_2 = ?,
            id_products_3 = ?,
            id_products_4 = ?,
            id_products_5 = ?
            WHERE id_gift = ?',$data);
    }
    public function themQuaTang($data){
        return DB::insert('INSERT INTO gift (
            id_gift,
            name_products,
            id_products_1,
            id_products_2,
            id_products_3,
            id_products_4,
            id_products_5) values (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?)', $data);
    }
}
