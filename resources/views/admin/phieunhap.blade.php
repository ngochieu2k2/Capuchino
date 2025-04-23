@extends('admin.layouts.client')
@section('title')
    Phiếu nhập
@endsection
@section('head')
    {{-- thêm css --}}
    <style>
        .form-button-action *{
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
    </style>
@endsection
@section('content')
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Phiếu Nhập Hàng</h2>
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
                            <a class="btn btn-primary btn-round ml-auto" href="{{ route('themphieunhap') }}">
                                <i class="fa fa-plus"></i>
                                &nbsp;Lập Phiếu Nhập
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="add-row" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">Mã</th>
                                        <th width="17%">Thời gian</th>
                                        <th>Tên nhà cung cấp</th>
                                        <th width="13%">Trạng thái</th>
                                        <th width="20%">Tổng tiền</th>
                                        <th width="20%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Thời gian</th>
                                        <th>Tên nhà cung cấp</th>
                                        <th>Trạng thái</th>
                                        <th>Tổng tiền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @if (!empty($danhSachPhieuNhap))
                                        @foreach ($danhSachPhieuNhap as $phieuNhap)
                                            <tr>
                                                <td>PN{{ $phieuNhap->id_purchase_order }}</td>
                                                <td>{{ date('H:i d/m/Y', strtotime($phieuNhap->date_created)) }}</td>
                                                @if (!empty($danhSachNguoiDung))
                                                    @foreach ($danhSachNguoiDung as $nguoiDung)
                                                        @if ($nguoiDung->id_users == $phieuNhap->id_users)
                                                            <td class="cantrai">{{ $nguoiDung->name_users }}</td>
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if ($phieuNhap->debt == 0)
                                                <td>Đã thanh toán</td>
                                            @else
                                                <td>Công nợ</td>
                                            @endif
                                            <td class="canphai">{{ number_format($phieuNhap->total_money, 0, ',') }}
                                            </td>
                                            <td>
                                                <div class="form-button-action">
                                                    <!-- <a target="_blank"
                                                        href="{{ url('inphieunhap?mapn=' . $phieuNhap->id_purchase_order) }}"
                                                        title="Chỉnh sửa" class="btn btn-link btn-success btn-lg">
                                                        <i class="fa fa-print"></i>
                                                    </a> -->
                                                    <a href="{{ url('suaphieunhap?id=' . $phieuNhap->id_purchase_order) }}"
                                                        title="Chỉnh sửa" class="btn btn-link btn-primary btn-lg">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button type="button" data-toggle="modal" title="Xóa"
                                                        class="btn btn-link btn-danger" data-original-title="Xóa"
                                                        data-target="#xoaPhieuNhap{{ $phieuNhap->id_purchase_order }}">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('admin.layouts.xoaphieunhap')
                                    @endforeach
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
