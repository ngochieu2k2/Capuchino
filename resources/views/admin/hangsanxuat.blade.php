@extends('admin.layouts.client')
@section('title')
    Hãng sản xuất
@endsection
@section('head')
    {{-- thêm css --}}
@endsection
@section('content')
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Hãng Sản Xuất</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-inner mt--5">
        <div class="row mt--2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Danh sách</h4>
                            <button class="btn btn-primary btn-round ml-auto" data-toggle="modal"
                                data-target="#themHangSanXuat">
                                <i class="fa fa-plus"></i>
                                &nbsp;Thêm Hãng
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('admin.layouts.themhangsanxuat')
                        <div class="table-responsive">
                            <table id="add-row" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">Mã</th>
                                        <th>Tên</th>
                                        <th width="15%">Loại</th>
                                        <th width="20%">Số sản phẩm thuộc hãng</th>
                                        <th width="25%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Tên</th>
                                        <th>Loại</th>
                                        <th>Số sản phẩm thuộc hãng</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @if (!empty($danhSachHangSanXuat))
                                        @foreach ($danhSachHangSanXuat as $hangSanXuat)
                                            <tr>
                                                <td>HSX{{ $hangSanXuat->id_mfg }}</td>
                                                <td>{{ $hangSanXuat->name_mfg }}</td>
                                                <td>{{ $hangSanXuat->cat_mfg == 0 ? 'điện thoại' : 'Phu Kiện' }}</td>
                                                <td>
                                                    @php
                                                        $soSanPhamThuocHang = 0;
                                                    @endphp
                                                    @if (!empty($danhSachSanPham))
                                                        @foreach ($danhSachSanPham as $sanPham)
                                                            @if ($sanPham->id_mfg == $hangSanXuat->id_mfg)
                                                                @php
                                                                    $soSanPhamThuocHang++;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    {{ $soSanPhamThuocHang }}
                                                </td>
                                                <td>
                                                    <div class="form-button-action">
                                                        <button type="button" data-toggle="modal" title="Xóa"
                                                            class="btn btn-link btn-danger" data-original-title="Xóa"
                                                            onclick="xoaHangSanXuat('{{ $hangSanXuat->name_mfg }}','{{ $hangSanXuat->id_mfg }}','{{ $hangSanXuat->cat_mfg }}','{{ $soSanPhamThuocHang }}')"
                                                            data-target="#xoaHangSanXuat">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                        <!-- Nút Sửa -->
                                                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                            data-target="#suaHangSanXuat{{ $hangSanXuat->id_mfg }}">Sửa</button>

                                                        <!-- Modal Sửa -->
                                                        <div class="modal fade"
                                                            id="suaHangSanXuat{{ $hangSanXuat->id_mfg }}" tabindex="-1"
                                                            role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <form action="{{ route('xulyhangsanxuat') }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="thaoTac"
                                                                            value="sửa hãng sản xuất">
                                                                        <input type="hidden" name="maHangSua"
                                                                            value="{{ $hangSanXuat->id_mfg }}">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Sửa hãng sản xuất</h4>
                                                                            <button type="button" class="close"
                                                                                data-dismiss="modal">&times;</button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <label>Tên hãng</label>
                                                                                <input type="text" name="tenHang"
                                                                                    class="form-control"
                                                                                    value="{{ $hangSanXuat->name_mfg }}"
                                                                                    required>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label>Loại hãng</label>
                                                                                <select name="loaiHang" class="form-control"
                                                                                    required>
                                                                                    <option value="0"
                                                                                        {{ $hangSanXuat->cat_mfg == 0 ? 'selected' : '' }}>
                                                                                        điện thoại</option>
                                                                                    <option value="1"
                                                                                        {{ $hangSanXuat->cat_mfg == 1 ? 'selected' : '' }}>
                                                                                        Phụ kiện</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">Đóng</button>
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Lưu</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @include('admin.layouts.xoahangsanxuat')
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    {{-- thêm js --}}
@endsection
