@extends('admin.layouts.client')

@section('title', 'Mã giảm giá')

@section('content')
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">Mã giảm giá</h2>
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
                                data-target="#createCoupon">
                                <i class="fa fa-plus"></i>
                                &nbsp;Thêm Mã Giảm Giá
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            @include('admin.coupon.create')
                            @include('admin.coupon.edit')
                            <div class="table-responsive">
                                <table id="add-row" class="display table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="10%">Mã</th>
                                            <th width="13%">Ngày bắt đầu</th>
                                            <th width="13%">Ngày kết thúc</th>
                                            <th>Số vé</th>
                                            <th width="12%">Số tiền giảm</th>
                                            <th width="10%">Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Mã</th>
                                            <th>Ngày bắt đầu</th>
                                            <th>Ngày kết thúc</th>
                                            <th>Số vé</th>
                                            <th>Số tiền giảm</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @if (count($coupons) > 0)
                                            @foreach ($coupons as $item)
                                                <tr class="{{ $item->is_active == 1 ? '' : 'table-danger' }}">
                                                    <td>{{ $item->code }}</td>
                                                    <td>{{ $item->start_date->format('d/m/Y') }}</td>
                                                    <td>{{ $item->end_date->format('d/m/Y') }}</td>
                                                    <td>{{ $item->usage_limit }}</td>
                                                    <td>{{ $item->discount }}</td>
                                                    <td>
                                                        @if ($item->is_active == 1)
                                                            <span class="badge badge-success">Hoạt động</span>
                                                        @else
                                                            <span class="badge badge-danger">Khóa</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($item->is_active == 1)
                                                            <form action="{{ route('coupon.lock', $item->id) }}"
                                                                method="POST" style="display:inline;"
                                                                class="lock-coupon-form">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-danger">Khóa</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
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
    </div>

@section('scripts')
    <script>
        document.querySelectorAll('.lock-coupon-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!confirm('Bạn có chắc chắn muốn khóa mã giảm giá này?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
@endsection
