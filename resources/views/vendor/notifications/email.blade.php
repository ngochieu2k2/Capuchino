<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Xác nhận địa chỉ Email</title>
    <style>
        /* CSS căn bản để email hiển thị đẹp và nút bấm */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        h1 {
            color: #333;
        }

        p {
            color: #555;
            line-height: 1.5em;
        }

        .button {
            display: inline-block;
            background-color: #1d3557;
            color: white !important;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin: 20px 0;
        }

        .footer {
            font-size: 1em;
            color: #999;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .break-word {
            word-break: break-all;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Xin chào!</h1>

        <p>Vui lòng nhấn nút bên dưới để xác nhận địa chỉ email của bạn.</p>

        <p style="text-align:center;">
            <a href="{{ $actionUrl }}" class="button" target="_blank" rel="noopener noreferrer">
                Xác nhận địa chỉ Email
            </a>
        </p>

        <p>Nếu bạn không tạo tài khoản, bạn không cần làm gì thêm.</p>

        <p>Trân trọng,<br>King Store</p>

        @if (isset($actionUrl))
            <div class="footer">
                <p>Nếu bạn gặp khó khăn khi nhấn nút "Xác nhận địa chỉ Email", hãy sao chép và dán đường dẫn bên dưới
                    vào trình duyệt web của bạn:</p>
                <p><a href="{{ $actionUrl }}">{{ $actionUrl }}</a></p>
            </div>
        @endif
    </div>
</body>

</html>
