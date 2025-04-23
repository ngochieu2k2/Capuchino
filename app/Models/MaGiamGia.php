<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class MaGiamGia extends Model
{
    use HasFactory;
    public function layDanhSachMaGiamGia(){
        $danhSachMaGiamGia = DB::select('SELECT * FROM discount ORDER BY id_discount DESC');
        return $danhSachMaGiamGia;
    }
    public function timMaGiamGiaTheoMa($magiamgia){
        $maGiamGia = DB::select('SELECT * FROM discount WHERE id_discount = ?',[$magiamgia]);
        if(!empty($maGiamGia)){
            return $maGiamGia[0];
        }
        return $maGiamGia;
    }
    public function suaMaGiamGia($data,$magiamgia){
        $data = array_merge($data,[$magiamgia]);
        return DB::select('UPDATE discount SET
            describes = ?,
            start_date = ?,
            end_date = ?
            WHERE id_discount = ?',$data);
    }
    public function xoaMaGiamGia($magiamgia){
        return DB::select('DELETE FROM discount WHERE id_discount = ?',[$magiamgia]);
    }
    public function themMaGiamGia($data){
        return DB::insert('INSERT INTO discount (
            id_discount,
            describes,
            reduced_price,
            start_date,
            end_date) values (
            ?,
            ?,
            ?,
            ?,
            ?)', $data);
    }
}
