<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\LoiPhanHoi;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedBackController extends Controller
{
    public function index()
    {
        $feedbacks = (new LoiPhanHoi())->layDanhSachLoiPhanHoiTheoBoLoc();
        return view('admin.feedback.index', compact('feedbacks'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        $model = new LoiPhanHoi();
        $feedback = $model->timLoiPhanHoiTheoMa($id);

        // Lấy email khách hàng
        $user = NguoiDung::where('id_users', $feedback->id_users ?? null)->first();
        if (!$user || !$user->email) {
            return back()->with('error', 'Không tìm thấy email khách hàng.');
        }
        if ($feedback->status == 1) {
            return back()->with('error', 'Feedback này đã được phản hồi.');
        }

        // Gửi email
        Mail::raw($request->message, function ($mail) use ($user, $request) {
            $mail->to($user->email)
                ->subject($request->title);
        });

        // Cập nhật status
        $model->doiTrangThaiLoiPhanHoi([1], $id);

        return back()->with('success', 'Đã gửi phản hồi thành công!');
    }
}
