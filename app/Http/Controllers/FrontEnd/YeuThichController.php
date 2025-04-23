<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SanPham;
use App\Models\ThuVienHinh;
use App\Models\HangSanXuat;

class YeuThichController extends Controller
{
    private $sanPham;
    private $thuVienHinh;
    private $hangSanXuat;
    public function __construct()
    {
        $this->sanPham = new SanPham();
        $this->thuVienHinh = new ThuVienHinh();
        $this->hangSanXuat = new HangSanXuat();
    }
    public function xulyyeuthich(Request $request)
    {
        $request->validate(['thaotac' => 'required|string']);
        if ($request->thaotac == "boyeuthich") { // *******************************************************************************************bo yeu thich
            $rules = [
                'masp' => 'required|integer|exists:products,id_products'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'integer' => ':attribute đã nhập sai'
            ];
            $attributes = [
                'masp' => 'Mã sản phẩm'
            ];
            $request->validate($rules, $messages, $attributes);
            $yeuThich = [];
            $flag = false;
            if (!empty(session('yeuThich'))) {
                foreach (session('yeuThich') as $ctyt) { // duyet qua gio hang cu
                    if ($request->masp == $ctyt['id_products']) { //neu chi tiet gio hang co ma san pham trung voi san pham can xoa trong gio hang thi khong dc them vao gio hang moi
                        $flag = true;
                    } else {  //con neu chi tiet gio hang khac voi san pham can xoa trong gio hang
                        $yeuThich = array_merge($yeuThich, [$ctyt]); // thi them chi tiet gio hang do vao gio
                    }
                }
                if ($flag) {
                    session(['yeuThich' => $yeuThich]); //thay gio hang cu bang gio hang moi
                    return back()->with('thongbao', 'Bỏ yêu thích SP' . $request->masp . ' thành công!');
                }
            }
            if (empty(session('yeuThich'))) {
                session()->forget('yeuThich');
            }
            return back()->with('thongbao', 'Bỏ yêu thích SP' . $request->masp . ' thất bại!');
        }
        if ($request->thaotac == "yeuthich") { // *******************************************************************************************yeu thich
            $rules = [
                'masp' => 'required|integer|exists:products,id_products'
            ];
            $messages = [
                'required' => ':attribute bắt buộc nhập',
                'exists' => ':attribute không tồn tại',
                'integer' => ':attribute đã nhập sai'
            ];
            $attributes = [
                'masp' => 'Mã sản phẩm'
            ];
            $request->validate($rules, $messages, $attributes);
            $thongTinSanPham = $this->sanPham->timSanPhamTheoMa($request->masp); //tim san pham da them vao yeu thich
            $thongTinHinh = $this->thuVienHinh->timThuVienHinhTheoMa($thongTinSanPham->id_photo); //tim hinh san pham da them vao yeu thich
            // $thongTinHangSanXuat=$this->hangSanXuat->timHangSanXuatTheoMa($request->id_mfg);
            if (!empty($thongTinSanPham) && !empty($thongTinHinh)) {
                $chiTietYeuThich = [
                    'id_products' => $thongTinSanPham->id_products,
                    // 'id_mfg' => $thongTinHangSanXuat->id_mfg,
                    'name_products' => $thongTinSanPham->name_products,
                    'sale_price' => $thongTinSanPham->sale_price,
                    'promotional_price' => $thongTinSanPham->promotional_price,
                    'qty' => $thongTinSanPham->qty,
                    'photo' => $thongTinHinh->photo_1
                ];
                $yeuThich = [];
                if (!empty(session('yeuThich'))) {
                    foreach (session('yeuThich') as $ctyt) {
                        if ($ctyt['id_products'] == $chiTietYeuThich['id_products']) { // tim xem chi tiet gio hang vua them co san trong gio hang chua
                            return back()->with('thongbao', 'SP' . $request->masp . ' đã có trong danh sách yêu thích!');
                        }
                        $yeuThich = array_merge($yeuThich, [$ctyt]);
                    }
                }
                $yeuThich = array_merge($yeuThich, [$chiTietYeuThich]);
                session(['yeuThich' => $yeuThich]);
                return back()->with('thongbao', 'Yêu thích SP' . $request->masp . ' thành công!');
            }
        }
        return redirect()->route('/')->with('thongbao', 'Thao tác thất bại vui lòng thử lại!');
    }
}
