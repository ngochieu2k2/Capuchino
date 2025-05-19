@extends('admin.layouts.client')

@section('title', 'Phản hồi khách hàng')

@section('content')
    <div class="container mt-4">
        <h4>Danh sách phản hồi khách hàng</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nội dung</th>
                    <th>Khách hàng</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th>Phản hồi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($feedbacks as $fb)
                    <tr @if ($fb->status == 1) style="background: #e9ecef;" @endif>
                        <td>{{ $fb->content }}</td>
                        <td>{{ $fb->name_users ?? 'Ẩn danh' }}</td>
                        <td>{{ $fb->date_created }}</td>
                        <td>
                            @if ($fb->status == 1)
                                <span class="badge badge-success">Đã phản hồi</span>
                            @else
                                <span class="badge badge-warning">Chưa phản hồi</span>
                            @endif
                        </td>
                        <td>
                            @if ($fb->status == 0)
                                <button class="btn btn-primary btn-sm" data-toggle="modal"
                                    data-target="#replyModal{{ $fb->id_feedback }}">Phản hồi</button>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>Đã gửi</button>
                            @endif

                            <!-- Modal -->
                            <div class="modal fade" id="replyModal{{ $fb->id_feedback }}" tabindex="-1" role="dialog"
                                aria-labelledby="replyModalLabel{{ $fb->id_feedback }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form method="POST" action="{{ route('feedback.reply', $fb->id_feedback) }}">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="replyModalLabel{{ $fb->id_feedback }}">Phản hồi
                                                    khách hàng</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Tiêu đề</label>
                                                    <input type="text" name="title" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Nội dung phản hồi</label>
                                                    <textarea name="message" class="form-control" rows="4" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
