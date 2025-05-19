@extends('admin.layouts.client')
@section('title')
    Tổng quan
@endsection
@section('content')
    <div class="container-fluid mt-4">
        {{-- Dropdown filter --}}
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
                <form method="get" class="form-inline">
                    <div class="dropdown">
                        @php
                            $filterText = [
                                'day' => 'Ngày',
                                'week' => 'Tuần',
                                'month' => 'Tháng',
                                'year' => 'Năm',
                            ];
                        @endphp
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="filterDropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $filterText[$filter] ?? 'Tuần' }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="filterDropdown">
                            <a class="dropdown-item {{ $filter == 'day' ? 'active' : '' }}"
                                href="{{ route('tongquan', ['filter' => 'day']) }}">Ngày</a>
                            <a class="dropdown-item {{ $filter == 'week' ? 'active' : '' }}"
                                href="{{ route('tongquan', ['filter' => 'week']) }}">Tuần</a>
                            <a class="dropdown-item {{ $filter == 'month' ? 'active' : '' }}"
                                href="{{ route('tongquan', ['filter' => 'month', 'year' => $year]) }}">Tháng</a>
                            <a class="dropdown-item {{ $filter == 'year' ? 'active' : '' }}"
                                href="{{ route('tongquan', ['filter' => 'year']) }}">Năm</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tổng quan --}}
        <div class="row dashboard-row">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success h-100">
                    <div class="card-body">
                        <div class="card-title">Tổng sản phẩm</div>
                        <h4>{{ $tongSanPham }}</h4>
                        <small>
                            @foreach ($sanPhamTheoDanhMuc as $cat => $so)
                                <span class="badge badge-light">
                                    {{ $cat == 0 ? 'Laptop' : 'Phụ kiện' }}: {{ $so }}
                                </span>
                            @endforeach
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info h-100">
                    <div class="card-body">
                        <div class="card-title">Tổng đơn hàng</div>
                        <h4>{{ $tongDonHang }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body">
                        <div class="card-title">Tổng doanh thu</div>
                        <h4>{{ number_format($tongDoanhThu, 0, ',', '.') }}đ</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body">
                        <div class="card-title">Tổng khách hàng</div>
                        <h4>{{ $tongKhachHang }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tỷ lệ đơn hàng theo trạng thái --}}
        <div class="row dashboard-row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <b>Tỷ lệ đơn hàng theo trạng thái</b>
                    </div>
                    <div class="card-body">
                        <canvas id="trangThaiDon"></canvas>
                        <div class="mt-3">
                            <span class="badge badge-secondary">Chờ xác nhận: {{ $trangThaiDon['Chờ xác nhận'] }}</span>
                            <span class="badge badge-primary">Đang giao: {{ $trangThaiDon['Đang giao'] }}</span>
                            <span class="badge badge-success">Hoàn thành: {{ $trangThaiDon['Hoàn thành'] }}</span>
                            <span class="badge badge-danger">Đã huỷ: {{ $trangThaiDon['Đã huỷ'] }}</span>
                            <span class="badge badge-info">Tỷ lệ hoàn thành: {{ $tyLeHoanThanh }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Biểu đồ số lượng đơn hàng --}}
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <b>Biểu đồ số lượng đơn hàng</b>
                    </div>
                    <div class="card-body">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biểu đồ doanh thu --}}
        <div class="row dashboard-row">
            <div class="col-md-12 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <b>Biểu đồ doanh thu</b>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 5 sản phẩm bán chạy --}}
        <div class="row dashboard-row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <b>Top 5 sản phẩm bán chạy</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topSanPhamBanChay as $i => $sp)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            @php
                                                $sanPham = \App\Models\SanPham::where(
                                                    'id_products',
                                                    $sp->id_products,
                                                )->first();
                                            @endphp
                                            {{ $sanPham ? $sanPham->name_products : '---' }}
                                        </td>
                                        <td>{{ $sp->so_luong }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Top 5 khách hàng mua nhiều nhất --}}
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <b>Top 5 khách hàng mua nhiều nhất</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Khách hàng</th>
                                    <th>Số đơn</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topKhachHang as $i => $kh)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            @php
                                                $user = \App\Models\User::find($kh->id_users);
                                            @endphp
                                            {{ $user ? $user->name_users : '---' }}
                                        </td>
                                        <td>{{ $kh->so_don }}</td>
                                        <td>{{ number_format($kh->tong_tien, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Biểu đồ số lượng đơn hàng
        const ordersLabels = @json($labels);
        const ordersData = @json($ordersData);
        new Chart(document.getElementById('ordersChart'), {
            type: 'bar',
            data: {
                labels: ordersLabels,
                datasets: [{
                    label: 'Số đơn hàng',
                    data: ordersData,
                    backgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Biểu đồ doanh thu
        const revenueLabels = @json($labels);
        const revenueData = @json($revenueData);
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Biểu đồ tròn trạng thái đơn hàng
        const trangThaiDon = @json(array_values($trangThaiDon));
        new Chart(document.getElementById('trangThaiDon'), {
            type: 'doughnut',
            data: {
                labels: ['Chờ xác nhận', 'Đang giao', 'Hoàn thành', 'Đã huỷ'],
                datasets: [{
                    data: trangThaiDon,
                    backgroundColor: ['#6c757d', '#007bff', '#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
@endsection
