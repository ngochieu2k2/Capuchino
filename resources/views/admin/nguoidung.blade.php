@extends('admin.layouts.client')
@section('title')
    Người dùng
@endsection
@section('head')
    {{-- thêm css --}}
@endsection
@section('content')
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Người Dùng</h2>
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
                                data-target="#themNguoiDung">
                                <i class="fa fa-plus"></i>
                                &nbsp;Thêm Người Dùng
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('admin.layouts.themnguoidung')
                        <div class="table-responsive">
                            <table id="add-row" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="2%">Mã</th>
                                        <th>Tên</th>
                                        <th width="15%">Loại người Dùng</th>
                                        <th width="10%">SĐT</th>
                                        <th width="25%">Địa chỉ</th>
                                        <th width="10%">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Tên</th>
                                        <th>Loại người dùng</th>
                                        <th>SĐT</th>
                                        <th>Địa chỉ</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @if (!empty($danhSachNguoiDung))
                                        @foreach ($danhSachNguoiDung as $nguoiDung1)
                                            <tr>
                                                <td>ND{{ $nguoiDung1->id_users }}</td>
                                                <td class="cantrai">{{ $nguoiDung1->name_users }}</td>
                                                @if ($nguoiDung1->roles == 0 && $nguoiDung1->email==NULL)
                                                    <td class="cangiua">Khách Vãng Lai</td>
                                                @elseif ($nguoiDung1->roles == 0)
                                                    <td class="cangiua">Khách Hàng</td>
                                                @elseif ($nguoiDung1->roles == 1)
                                                    <td class="cangiua">Đối Tác</td>
                                                @elseif ($nguoiDung1->roles == 2)
                                                    <td class="cangiua">Nhân Viên</td>
                                                @endif
                                                <td class="cangiua">{{ $nguoiDung1->phone }}</td>

                                                <td class="cantrai" title="">{{ $nguoiDung1->address }}
                                                </td>
                                                <td>
                                                    @if ($nguoiDung1->status == 1)
                                                        <button type="button" class="btn btn-success btn-border"
                                                            data-toggle="modal" title="Đổi trạng thái"
                                                            data-original-title="Đổi trạng thái" style="width:99%;"
                                                            onclick="doiTrangThaiNguoiDung('{{ $nguoiDung1->name_users }}','{{ $nguoiDung1->id_users }}','{{ $nguoiDung1->status }}')"
                                                            data-target="#doiTrangThaiNguoiDung">
                                                            <i class="fa fa-unlock"></i>&nbsp;&nbsp;Đang hoạt động
                                                        </button>
                                                    @elseif ($nguoiDung1->status == 0)
                                                        <button type="button" class="btn btn-warning btn-border"
                                                            data-toggle="modal" title="Đổi trạng thái"
                                                            data-original-title="Đổi trạng thái" style="width:99%;"
                                                            onclick="doiTrangThaiNguoiDung('{{ $nguoiDung1->name_users }}','{{ $nguoiDung1->id_users }}','{{ $nguoiDung1->status }}')"
                                                            data-target="#doiTrangThaiNguoiDung">
                                                            <i class="fa fa-lock"></i>&nbsp;&nbsp;Đang bị khóa
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @include('admin.layouts.doitrangthainguoidung')
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
    <script>
        $(document).ready(function() {
            $('#loaiNguoiDung').change(function() {
                var loaiNguoiDung = document.getElementById('loaiNguoiDung');
                var email = document.getElementById('email');
                var matKhau = document.getElementById('matKhau');
                var nhapLaiMatKhau = document.getElementById('nhapLaiMatKhau');
                if (loaiNguoiDung.value == 2) {
                    $('#taiKhoanNhanVien').removeClass("displaynone");
                    email.required = true;
                    matKhau.required = true;
                    nhapLaiMatKhau.required = true;
                } else {
                    $('#taiKhoanNhanVien').addClass("displaynone");
                    email.required = false;
                    matKhau.required = false;
                    nhapLaiMatKhau.required = false;
                }
            });
            $('#btnThemNguoiDung').click(function() {
                var matKhau = document.getElementById('matKhau');
                var nhapLaiMatKhau = document.getElementById('nhapLaiMatKhau');
                if (matKhau.value != nhapLaiMatKhau.value) {
                    nhapLaiMatKhau.value = null;
                    alert("Mật khẩu và nhập lại mật khẩu không khớp nhau!");
                }
            });
        });
    </script>
@endsection
