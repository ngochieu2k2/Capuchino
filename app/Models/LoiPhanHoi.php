<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoiPhanHoi extends Model
{
    use HasFactory;
    protected $table = 'feedback';
    public function layDanhSachLoiPhanHoiTheoBoLoc($boLoc = [])
    {
        $danhSachLoiPhanHoi = DB::table($this->table);
        $danhSachLoiPhanHoi = $danhSachLoiPhanHoi->select(DB::raw($this->table . '.* , users.name_users,users.phone,users.address'))
        ->leftJoin('users', $this->table . '.id_users', '=', 'users.id_users');
        if (!empty($boLoc)) {
            foreach ($boLoc as $bl) {
                if (count($bl) == 2) {
                    $danhSachLoiPhanHoi = $danhSachLoiPhanHoi->whereIn($bl[0], $bl[1]);
                } else if (count($bl) == 3) {
                    $danhSachLoiPhanHoi = $danhSachLoiPhanHoi->where([$bl]);
                }
            }
        }
        $danhSachLoiPhanHoi = $danhSachLoiPhanHoi->orderBy($this->table . '.status', 'ASC');
        $danhSachLoiPhanHoi = $danhSachLoiPhanHoi->orderBy($this->table . '.date_created', 'DESC');
        $danhSachLoiPhanHoi = $danhSachLoiPhanHoi->get()->all();
        return $danhSachLoiPhanHoi;
    }
    public function timLoiPhanHoiTheoMa($maloiphanhoi){
        $loiPhanHoi = DB::select('SELECT * FROM feedback WHERE id_feedback = ?',[$maloiphanhoi]);
        if(!empty($loiPhanHoi)){
            return $loiPhanHoi[0];
        }
        return $loiPhanHoi;
    }
    public function doiTrangThaiLoiPhanHoi($data,$maloiphanhoi){
        $data = array_merge($data,[$maloiphanhoi]);
        return DB::select('UPDATE feedback SET
            status = ?
            WHERE id_feedback = ?',$data);
    }
    public function doiTrangThaiLoiPhanHoiTatCa(){
        return DB::select('UPDATE feedback SET
            status = 1
            WHERE status = 0');
    }
    public function themLoiPhanHoi($data){
        return DB::insert('INSERT INTO feedback (
            content,
            status,
            id_users,
            date_created) values (
            ?,
            ?,
            ?,
            ?)', $data);
    }
}
