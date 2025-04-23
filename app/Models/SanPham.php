<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SanPham extends Model
{
    use HasFactory;
    protected $table = 'products';
    public function layDanhSachSanPham()
    {
        $danhSachSanPham = DB::select('SELECT * FROM products ORDER BY id_products DESC');
        return $danhSachSanPham;
    }
    public function layDanhSachSanPhamTheoBoLoc($boLoc = [], $tuKhoa = NULL, $sapXep = NULL, $mucGia = [], $tinhTrang = [], $nhuCau = [], $manHinh = [], $oCung = [], $cardDoHoa = [], $Ram = [], $Cpu = [])
    {
        $danhSachSanPham = DB::table($this->table);
        if (!empty($boLoc)) {
            foreach ($boLoc as $bl) {
                if (count($bl) == 2) {
                    $danhSachSanPham = $danhSachSanPham->whereIn($bl[0], $bl[1]);
                } else if (count($bl) == 3) {
                    $danhSachSanPham = $danhSachSanPham->where([$bl]);
                }
            }
        }
        if (!empty($tinhTrang) || !empty($nhuCau) || !empty($manHinh) || !empty($oCung) || !empty($cardDoHoa) || !empty($Ram) || !empty($Cpu)) {
            $danhSachSanPham = $danhSachSanPham->select(DB::raw($this->table . '.* , laptop.*'))
                ->leftJoin('laptop', $this->table . '.id_laptop', '=', 'laptop.id_laptop');
            if (count($Cpu) > 0) {
                $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($Cpu) {
                    foreach ($Cpu as $cpu) {
                        $query->orWhere([$cpu]);
                    }
                });
            }
            if (count($Ram) > 0) {
                $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($Ram) {
                    foreach ($Ram as $ram) {
                        $query->orWhere([$ram]);
                    }
                });
            }
            if (count($cardDoHoa) > 0) {
                $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($cardDoHoa) {
                    foreach ($cardDoHoa as $cdh) {
                        $query->orWhere([$cdh]);
                    }
                });
            }
            if (count($oCung) > 0) {
                $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($oCung) {
                    foreach ($oCung as $oc) {
                        $query->orWhere([$oc]);
                    }
                });
            }
            if (count($manHinh) > 0) {
                $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($manHinh) {
                    foreach ($manHinh as $mh) {
                        $query->orWhere(function ($query1) use ($mh) {
                            $query1->whereBetween('laptop.screen', [$mh]);
                        });
                    }
                });
            }
            if (count($nhuCau) > 0) {
                $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($nhuCau) {
                    foreach ($nhuCau as $nc) {
                        $query->orWhere([$nc]);
                    }
                });
            }
            if (count($tinhTrang) > 0) {
                $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($tinhTrang) {
                    foreach ($tinhTrang as $tt) {
                        $query->orWhere([$tt]);
                    }
                });
            }
        }
        if (!empty($mucGia)) {
            $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($mucGia) {
                for ($i = 0; $i < count($mucGia); $i++) {
                    $query->orWhere(function ($query1) use ($mucGia, $i) {
                        $query1->whereBetween(
                            DB::raw('IF(' . $this->table . '.promotional_price>0, ' . $this->table . '.promotional_price, ' . $this->table . '.sale_price) '),
                            [$mucGia[$i][0], $mucGia[$i][1]]
                        );
                    });
                }
            });
        }
        if (!empty($tuKhoa)) {
            if (strpos(' ' . $tuKhoa, 'SP') > 0 || strpos(' ' . $tuKhoa, 'sp') > 0) {
                $tuKhoa = str_replace('SP', '', $tuKhoa);
                $tuKhoa = str_replace('sp', '', $tuKhoa);
                $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($tuKhoa) {
                    $query->orWhere($this->table . '.id_products', 'like', '%' . $tuKhoa . '%');
                });
                $danhSachSanPham = $danhSachSanPham->orderBy($this->table . '.id_products', 'ASC');
            } else {
                $danhSachSanPham = $danhSachSanPham->where(function ($query) use ($tuKhoa) {
                    $query->orWhere($this->table . '.name_products', 'like', '%' . $tuKhoa . '%');
                    $query->orWhere($this->table . '.describes', 'like', '%' . $tuKhoa . '%');
                    $query->orWhere($this->table . '.sale_price', 'like', '%' . $tuKhoa . '%');
                    $query->orWhere($this->table . '.promotional_price', 'like', '%' . $tuKhoa . '%');
                });
            }
        }
        if (!empty($sapXep)) {
            if ($sapXep == 'moinhat') {
                $danhSachSanPham = $danhSachSanPham->orderBy($this->table . '.date_created', 'DESC');
            } else if ($sapXep == 'banchaynhat') {
                $danhSachSanPham = $danhSachSanPham->select(DB::raw($this->table . '.* , SUM(invoice_details.qty) AS tongsoluongdaban'))
                    ->leftJoin('invoice_details', $this->table . '.id_products', '=', 'invoice_details.id_products')
                    ->where('invoice_details.dongia', '>', 0)
                    ->groupBy('invoice_details.id_products')
                    ->orderBy('tongsoluongdaban', 'DESC');
            } else if ($sapXep == 'uudainhat') {
                $danhSachSanPham = $danhSachSanPham->select(DB::raw('*,(' . $this->table . '.sale_price-' . $this->table . '.promotional_price) AS sotiendagiam'))
                    ->where($this->table . '.promotional_price', '>', 0)
                    ->orderBy('sotiendagiam', 'DESC');
            } else if ($sapXep == 'giatangdan') {
                $danhSachSanPham = $danhSachSanPham->select(DB::raw($this->table . '.* , IF(' . $this->table . '.promotional_price>0, '
                    . $this->table . '.promotional_price, ' . $this->table . '.sale_price) AS gia'));
                $danhSachSanPham = $danhSachSanPham->orderBy('gia', 'ASC');
            } else if ($sapXep == 'giagiamdan') {
                $danhSachSanPham = $danhSachSanPham->select(DB::raw($this->table . '.* , IF(' . $this->table . '.promotional_price>0, '
                    . $this->table . '.promotional_price, ' . $this->table . '.sale_price) AS gia'));
                $danhSachSanPham = $danhSachSanPham->orderBy('gia', 'DESC');
            }
        } else {
            $danhSachSanPham = $danhSachSanPham->orderBy($this->table . '.date_created', 'DESC');
        }
        $danhSachSanPham = $danhSachSanPham->get()->all();
        return $danhSachSanPham;
    }
    public function layDanhSachSanPhamChoPhieu()
    {
        $danhSachSanPham = DB::select('SELECT products.*,  sum(IF(invoice.delivery_status = 2 OR invoice.delivery_status = 3,invoice_details.qty,0)) AS khachdat
        FROM `products` LEFT JOIN invoice_details ON products.id_products = invoice_details.id_products LEFT JOIN invoice ON invoice_details.id_invoice = invoice.id_invoice
        GROUP BY products.id_products ORDER BY products.id_products DESC');
        return $danhSachSanPham;
    }
    public function timSanPhamTheoMa($masanpham)
    {
        $sanPham = DB::select('SELECT * FROM products WHERE id_products = ?', [$masanpham]);
        if (!empty($sanPham)) {
            return $sanPham[0];
        }
        return $sanPham;
    }
    public function timSanPhamTheoTen($tensanpham)
    {
        $sanPham = DB::select('SELECT * FROM products WHERE name_products = ?', [$tensanpham]);
        if (!empty($sanPham)) {
            return $sanPham[0];
        }
        return $sanPham;
    }
    public function xoaSanPham($masanpham)
    {
        return DB::select('DELETE FROM products WHERE id_products = ?', [$masanpham]);
    }
    public function suaSanPham($data, $masanpham)
    {
        $data = array_merge($data, [$masanpham]);

        return DB::select('UPDATE products SET
            name_products = ?,
            guarantee = ?,
            describes = ?,
            id_mfg = ?
            WHERE id_products = ?', $data);
    }
    public function capNhatGia($data, $masanpham)
    {
        $data = array_merge($data, [$masanpham]);
        return DB::select('UPDATE products SET
            sale_price = ?,
            promotional_price = ?
            WHERE id_products = ?', $data);
    }
    public function nhapHang($data, $masanpham)
    {
        $data = array_merge($data, [$masanpham]);
        return DB::select('UPDATE products SET
            qty = ?,
            entry_price = ?,
            sale_price = ?,
            promotional_price = ?
            WHERE id_products = ?', $data);
    }
    public function suaSoLuong($data, $masanpham)
    {
        $data = array_merge($data, [$masanpham]);
        return DB::select('UPDATE products SET
            qty = ?
            WHERE id_products = ?', $data);
    }
    public function themSanPham($data)
    {
        return DB::insert('INSERT INTO products (
            id_products ,
            name_products,
            guarantee,
            describes,
            qty,
            entry_price,
            sale_price,
            promotional_price,
            id_photo ,
            id_mfg ,
            id_gift ,
            id_laptop ,
            id_accessory ,
            cat_products,
            date_created) values (
            ? ,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ? ,
            ? ,
            ? ,
            ? ,
            ? ,
            ?,
            CURRENT_TIMESTAMP)', $data);
    }
}
