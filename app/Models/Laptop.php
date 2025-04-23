<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Laptop extends Model
{
    use HasFactory;
    public function layDanhSachLaptop(){
        $danhSachLaptop = DB::select('SELECT * FROM laptop ORDER BY id_laptop DESC');
        return $danhSachLaptop;
    }
    public function timLaptopTheoMa($malaptop){
        $laptop = DB::select('SELECT * FROM laptop WHERE id_laptop = ?',[$malaptop]);
        if(!empty($laptop)){
            return $laptop[0];
        }
        return $laptop;
    }
    public function timLaptopTheoTenSanPham($tenSanPham){
        $laptop = DB::select('SELECT * FROM laptop WHERE name_products = ?',[$tenSanPham]);
        if(!empty($laptop)){
            return $laptop[0];
        }
        return $laptop;
    }
    public function xoaLaptop($malaptop){
        return DB::select('DELETE FROM laptop WHERE id_laptop = ?',[$malaptop]);
    }
    public function suaLaptop($data,$malaptop){
        $data = array_merge($data,[$malaptop]);
        return DB::select('UPDATE laptop SET
            name_products = ?,
            cpu = ?,
            ram = ?,
            card_laptop = ?,
            disk_laptop = ?,
            screen = ?,
            demand = ?,
            status = ?
            WHERE id_laptop = ?',$data);
    }
    public function themLaptop($data){
        return DB::insert('INSERT INTO laptop (
            id_laptop,
            name_products,
            cpu,
            ram,
            card_laptop,
            disk_laptop,
            screen,
            demand,
            status) values (
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
