<!-- Modal sửa mã giảm giá -->
<div class="modal fade " id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header no-bd">
                <h4 class="modal-title">
                    <span class="fw-mediumbold">
                        Sửa mã giảm giá</span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Giả sử biến $coupon có dữ liệu coupon hiện tại --}}
            <form id="editForm" action="" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="code">Nhập mã giảm giá</label>
                            <input type="text" name="code" id="edit-code" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label for="discount">Số tiền giảm</label>
                            <input type="number" name="discount" id="edit-discount" class="form-control" min="0"
                                step="any" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="start_date">Ngày bắt đầu</label>
                            <input type="date" name="start_date" id="edit-start_date" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label for="end_date">Ngày kết thúc</label>
                            <input type="date" name="end_date" id="edit-end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="min_order_amount">Điều kiện đơn hàng</label>
                            <input type="number" name="min_order_amount" id="edit-min_order_amount"
                                class="form-control" min="0">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="usage_limit">Tổng số mã giảm giá</label>
                            <input type="number" name="usage_limit" id="edit-usage_limit" class="form-control"
                                min="0" required>
                        </div>
                        <div class="col-6">
                            <label for="user_limit">Giới hạn dùng cho mỗi tài khoản</label>
                            <input type="number" name="user_limit" id="edit-user_limit" class="form-control"
                                min="0" required>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-3">
                    <button class="btn btn-primary" type="submit">Cập nhật</button>
                </div>
            </form>


        </div>
    </div>
</div>
