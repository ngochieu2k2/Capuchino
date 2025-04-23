<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NguoiDung extends Model
{
    use HasFactory;
    public function layDanhSachNguoiDung()
    {
        $danhSachNguoiDung = DB::select('SELECT * FROM users ORDER BY id_users DESC');
        return $danhSachNguoiDung;
    }
    public function timNguoiDungTheoMa($manguoidung)
    {
        $nguoiDung = DB::select('SELECT * FROM users WHERE id_users = ?', [$manguoidung]);
        if (!empty($nguoiDung)) {
            return $nguoiDung[0];
        }
        return $nguoiDung;
    }
    public function timNguoiDungTheoSoDienThoai($sodienthoai)
    {
        $nguoiDung = DB::select('SELECT * FROM users WHERE phone = ?', [$sodienthoai]);
        if (!empty($nguoiDung)) {
            return $nguoiDung[0];
        }
        return $nguoiDung;
    }
    public function timNguoiDungTheoNgayTao($ngaytao)
    {
        $nguoiDung = DB::select('SELECT * FROM users WHERE date_created = ?', [$ngaytao]);
        if (!empty($nguoiDung)) {
            return $nguoiDung[0];
        }
        return $nguoiDung;
    }
    public function doiTrangThaiNguoiDung($data, $manguoidung)
    {
        $data = array_merge($data, [$manguoidung]);
        return DB::select('UPDATE users SET
        status = ?
        WHERE id_users = ?', $data);
    }
    public function xoaNguoiDung($manguoidung)
    {
        return DB::select('DELETE FROM users WHERE id_users = ?', [$manguoidung]);
    }
    public function taoTaiKhoanNguoiDung($data, $manguoidung)
    {
        $data = array_merge($data, [$manguoidung]);
        return DB::select('UPDATE users SET
            email = ?,
            password = ?
            WHERE id_users = ?', $data);
    }
    public function suaNguoiDung($data, $manguoidung)
    {
        $data = array_merge($data, [$manguoidung]);
        return DB::select('UPDATE users SET
            name_users = ?,
            phone = ?,
            address = ?,
            roles = ?,
            email = ?,
            password = ?
            WHERE id_users = ?', $data);
    }
    public function themNguoiDung($data)
    {
        return DB::insert('INSERT INTO users (
            id_users,
            name_users,
            phone,
            address,
            status,
            roles,
            email,
            password,
            date_created) values (
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
