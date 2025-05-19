<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{

    public function __construct(
        protected Coupon $model,
    ) {}
    public function index()
    {
        $coupons = $this->model->all();
        return view('admin.coupon.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['string', 'required'],
            'discount' => ['numeric', 'required'],
            'start_date' => ['date', 'required'],
            'end_date' => ['date', 'required', 'after_or_equal:start_date'],
            'usage_limit' => ['numeric', 'required'],
            'user_limit' => ['numeric', 'required'],
            'min_order_amount' => ['numeric', 'required'],
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.string' => 'Mã giảm giá phải là chuỗi ký tự.',

            'discount.required' => 'Vui lòng nhập số tiền giảm.',
            'discount.numeric' => 'Số tiền giảm phải là số.',

            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',

            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.',

            'usage_limit.required' => 'Vui lòng nhập số lượt dùng tối đa.',
            'usage_limit.numeric' => 'Số lượt dùng tối đa phải là số.',

            'use_limit.required' => 'Vui lòng nhập số lượt dùng mỗi người.',
            'use_limit.numeric' => 'Số lượt dùng mỗi người phải là số.',

            'min_order_amount.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
            'min_order_amount.numeric' => 'Giá trị đơn hàng tối thieu phải là số.',
        ]);
        //luu vao csdl
        $this->model->create($data);
        return redirect()->route('coupon.index')->with('success', 'Thêm mã giảm giá thành công.');
    }

    public function update(Request $request, $id)
    {
        $coupon = $this->model->findOrFail($id);

        $data = $request->validate([
            'code' => ['string', 'required'],
            'discount' => ['numeric', 'required'],
            'start_date' => ['date', 'required'],
            'end_date' => ['date', 'required', 'after_or_equal:start_date'],
            'usage_limit' => ['numeric', 'required'],
            'user_limit' => ['numeric', 'required'],
            'min_order_amount' => ['numeric', 'required'],
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.string' => 'Mã giảm giá phải là chuỗi ký tự.',

            'discount.required' => 'Vui lòng nhập số tiền giảm.',
            'discount.numeric' => 'Số tiền giảm phải là số.',

            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',

            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.',

            'usage_limit.required' => 'Vui lòng nhập số lượt dùng tối đa.',
            'usage_limit.numeric' => 'Số lượt dùng tối đa phải là số.',

            'user_limit.required' => 'Vui lòng nhập số lượt dùng mỗi người.',
            'user_limit.numeric' => 'Số lượt dùng mỗi người phải là số.',

            'min_order_amount.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
            'min_order_amount.numeric' => 'Giá trị đơn hàng tối thiểu phải là số.',
        ]);

        $coupon->update($data);

        return redirect()->route('coupon.index')->with('success', 'Cập nhật mã giảm giá thành công.');
    }

    public function lock($id)
    {
        $coupon = $this->model->findOrFail($id);
        $coupon->is_active = 0;
        $coupon->save();

        return redirect()->route('coupon.index')->with('success', 'Đã khóa mã giảm giá thành công.');
    }
}
