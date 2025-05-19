<!-- Modal them mau moi start-->
<div class="modal fade" id="createCoupon" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header no-bd">
                <h4 class="modal-title">
                    <span class="fw-mediumbold">
                        Thêm mã giảm giá mới</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('coupon.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="code">Nhập mã giảm giá</label>
                            <input type="text" name="code" id="code" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label for="discount">Số tiền giảm</label>
                            <input type="number" name="discount" id="discount" class="form-control" min="0"
                                step="any" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="start_date">Ngày bắt đầu</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label for="end_date">Ngày kết thúc</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="min_order_amount">Điều kiện đơn hàng</label>
                            <input type="number" name="min_order_amount" id="min_order_amount" class="form-control"
                                min="0" step="any" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="usage_limit">Tổng số mã giảm giá</label>
                            <input type="number" name="usage_limit" id="usage_limit" class="form-control"
                                min="0" required>
                        </div>
                        <div class="col-6">
                            <label for="user_limit">Giới hạn dùng cho mỗi tài khoản</label>
                            <input type="number" name="user_limit" id="user_limit" class="form-control" min="0"
                                required>
                        </div>
                    </div>
                </div>
                <div class="text-center mb-3">
                    <button class="btn btn-primary" type="submit">Thêm</button>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const usageLimitInput = document.getElementById('usage_limit');
        const userLimitInput = document.getElementById('user_limit');

        function validateLimits() {
            const usageLimit = parseInt(usageLimitInput.value) || 0;
            const userLimit = parseInt(userLimitInput.value) || 0;

            if (userLimit > usageLimit) {
                userLimitInput.setCustomValidity(
                    "Giới hạn mỗi tài khoản không được vượt quá tổng số mã giảm giá.");
            } else {
                userLimitInput.setCustomValidity("");
            }
        }

        // Gọi kiểm tra khi người dùng thay đổi input
        usageLimitInput.addEventListener('input', validateLimits);
        userLimitInput.addEventListener('input', validateLimits);
    });
</script>
