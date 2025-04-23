function doiTrangThaiNguoiDung(hoTenNguoiDungKhoa, maNguoiDungKhoa, trangThaiNguoiDungKhoa) {
    if (trangThaiNguoiDungKhoa == 0) { //dang bi khoa
        document.getElementById('tieuDeKhoa').innerHTML = 'Bạn có thực sự muốn mở khóa?';
        document.getElementById('noiDungKhoa').innerHTML = 'Thao tác này sẽ mở khóa [' + hoTenNguoiDungKhoa +
            '], nên cân nhắc trước khi thực hiện';
        document.getElementById('thaoTac').innerHTML = 'Mở khóa';
        $('#thaoTac').removeClass('btn-warning');
        $('#thaoTac').addClass('btn-success');
    }
    if (trangThaiNguoiDungKhoa == 1) { //dang hoat dong
        document.getElementById('tieuDeKhoa').innerHTML = 'Bạn có thực sự muốn khóa?';
        document.getElementById('noiDungKhoa').innerHTML = 'Thao tác này sẽ khóa [' + hoTenNguoiDungKhoa +
            '], các PHIẾU XUẤT chưa GIAO HÀNG THÀNH CÔNG sẽ chuyển thành ĐÃ HỦY, nên cân nhắc trước khi thực hiện';
        document.getElementById('thaoTac').innerHTML = 'Khóa';
        $('#thaoTac').removeClass('btn-success');
        $('#thaoTac').addClass('btn-warning');
    }
    document.getElementById('maNguoiDungKhoa').value = maNguoiDungKhoa;
};

function suaMaGiamGia(maGiamGia, hetHanSuDung) {
    document.getElementById('maGiamGiaSua').value = maGiamGia.id_discount;
    document.getElementById('maGiamGiaHien').innerHTML = maGiamGia.id_discount;
    document.getElementById('soTienGiamSua').value = maGiamGia.reduced_price;
    formatGia(document.getElementById('soTienGiamSua'));
    document.getElementById('soTienGiamHien').innerHTML = document.getElementById('soTienGiamSua').value;
    document.getElementById('ngayBatDauSua').min = maGiamGia.start_date;
    document.getElementById('ngayKetThucSua').min = maGiamGia.start_date;
    document.getElementById('ngayBatDauSua').value = maGiamGia.start_date;
    document.getElementById('ngayKetThucSua').value = maGiamGia.end_date;
    if (hetHanSuDung) {
        document.getElementById('hetHanCheck').checked = true;
        $('#divNgayBatDau').addClass('displaynone');
        $('#divNgayKetThuc').addClass('displaynone');
        document.getElementById('ngayBatDauSua').required = false;
        document.getElementById('ngayKetThucSua').required = false;
    } else {
        document.getElementById('hetHanCheck').checked = false;
        $('#divNgayBatDau').removeClass('displaynone');
        $('#divNgayKetThuc').removeClass('displaynone');
        document.getElementById('ngayBatDauSua').required = true;
        document.getElementById('ngayKetThucSua').required = true;
    }
    if (maGiamGia.describes != null) {
        document.getElementById('moTaSua').innerHTML = maGiamGia.describes;
    }
};

function xoaSanPham(tenSanPhamXoa, maSanPhamXoa) {
    document.getElementById('noiDungXoa').innerHTML = 'Thao tác này sẽ xóa sản phẩm [' + tenSanPhamXoa +
        '] vĩnh viễn và không thể khôi phục lại, nên cân nhắc trước khi xóa';
    document.getElementById('maSanPhamXoa').value = maSanPhamXoa;
};

function suaSanPham(sanPhamSua, cauHinhSua, hangSanXuatSua, thuVienHinhSua, quaTangSua) {
    var inputMaSanPhamSua = document.getElementById('maSanPhamSua');
    var inputTenSanPhamSua = document.getElementById('tenSanPhamSua');
    var inputBaoHanhSua = document.getElementById('baoHanhSua');
    var inputHangSanXuatSua = document.getElementById('hangSanXuatSua');
    inputMaSanPhamSua.value = sanPhamSua.id_products;
    inputTenSanPhamSua.value = sanPhamSua.name_products;
    inputBaoHanhSua.value = sanPhamSua.guarantee;
    inputBaoHanhSua.innerHTML = sanPhamSua.guarantee + ' tháng';
    inputHangSanXuatSua.value = hangSanXuatSua.id_mfg;
    inputHangSanXuatSua.innerHTML = hangSanXuatSua.name_mfg;
    if (sanPhamSua.cat_products == 0) { //la laptop
        var inputCpuSua = document.getElementById('cpuSua');
        var inputRamSua = document.getElementById('ramSua');
        var inputCardDoHoaSua = document.getElementById('cardDoHoaSua');
        var inputOCungSua = document.getElementById('oCungSua');
        var inputManHinhSua = document.getElementById('manHinhSua');
        var inputNhuCauSua = document.getElementById('nhuCauSua');
        var inputTinhTrangSua = document.getElementById('tinhTrangSua');
        inputCpuSua.value = cauHinhSua.cpu;
        inputRamSua.value = cauHinhSua.ram;
        inputRamSua.innerHTML = cauHinhSua.ram + ' GB';
        inputCardDoHoaSua.value = cauHinhSua.card_laptop;
        if (cauHinhSua.card_laptop == 0) {
            inputCardDoHoaSua.innerHTML = 'Onboard';
        } else if (cauHinhSua.card_laptop == 1) {
            inputCardDoHoaSua.innerHTML = 'Nvidia';
        } else if (cauHinhSua.card_laptop == 2) {
            inputCardDoHoaSua.innerHTML = 'Amd';
        }
        inputOCungSua.value = cauHinhSua.disk_laptop;
        inputOCungSua.innerHTML = cauHinhSua.disk_laptop + ' GB';
        inputManHinhSua.value = cauHinhSua.screen;
        inputNhuCauSua.value = cauHinhSua.demand;
        inputNhuCauSua.innerHTML = cauHinhSua.demand;
        inputTinhTrangSua.value = cauHinhSua.status;
        if (cauHinhSua.status == 0) {
            inputTinhTrangSua.innerHTML = 'Mới';
        } else if (cauHinhSua.status == 1) {
            inputTinhTrangSua.innerHTML = 'Cũ';
        }
    } else if (sanPhamSua.cat_products == 1) { //la phu kien
        var inputTenLoaiPhuKienSua = document.getElementById('tenLoaiPhuKienSua');
        inputTenLoaiPhuKienSua.innerHTML = cauHinhSua.cat_accessory;
    }
    if (thuVienHinhSua.photo_1 != null) {
        document.getElementById('photo_1').innerHTML = '<img class="thongtinhinhsua" src="img/sanpham/' +
            thuVienHinhSua.photo_1 + '">';
    } else {
        document.getElementById('photo_1').innerHTML = '';
    }
    if (thuVienHinhSua.photo_2 != null) {
        document.getElementById('photo_2').innerHTML = '<img class="thongtinhinhsua" src="img/sanpham/' +
            thuVienHinhSua.photo_2 + '">';
    } else {
        document.getElementById('photo_2').innerHTML = '';
    }
    if (thuVienHinhSua.photo_3 != null) {
        document.getElementById('photo_3').innerHTML = '<img class="thongtinhinhsua" src="img/sanpham/' +
            thuVienHinhSua.photo_3 + '">';
    } else {
        document.getElementById('photo_3').innerHTML = '';
    }
    if (thuVienHinhSua.photo_4 != null) {
        document.getElementById('photo_4').innerHTML = '<img class="thongtinhinhsua" src="img/sanpham/' +
            thuVienHinhSua.photo_4 + '">';

    } else {
        document.getElementById('photo_4').innerHTML = '';
    }
    if (thuVienHinhSua.photo_5 != null) {
        document.getElementById('photo_5').innerHTML = '<img class="thongtinhinhsua" src="img/sanpham/' +
            thuVienHinhSua.photo_5 + '">';
    } else {
        document.getElementById('photo_5').innerHTML = '';
    }
    for (var i = 0; i < 5; i++) {
        var inputQuaTangSua = document.getElementById('quaTangSua' + i);
        if (quaTangSua[i] != null) {
            inputQuaTangSua.selected = "true";
            inputQuaTangSua.value = quaTangSua[i].id_products;
            inputQuaTangSua.innerHTML = '[SP' + quaTangSua[i].id_products + '] - ' + quaTangSua[i].name_products;
        } else if (quaTangSua[i] === undefined) {
            inputQuaTangSua.selected = "true";
            inputQuaTangSua.value = null;
            inputQuaTangSua.innerHTML = 'Bỏ chọn sản phẩm ' + (i + 1);
        }
    }
    if (sanPhamSua.describes != null) {
        document.getElementById('moTaSua').innerHTML = sanPhamSua.describes;
    }
};

function xoaHangSanXuat(tenHangXoa, maHangXoa, loaiHangXoa, soSanPhamThuocHangXoa) {
    if (soSanPhamThuocHangXoa > 0) {
        if (loaiHangXoa == 0) {
            loaiHangXoa = 'LAPTOP';
        } else {
            loaiHangXoa = 'PHỤ KIỆN';
        }
        document.getElementById('noiDungXoa').innerHTML = 'Hãng sản xuất ' + tenHangXoa +
            ' có ' + soSanPhamThuocHangXoa +
            ' MẪU ' + loaiHangXoa + ' thuộc hãng này nên không thể tiến hành thao tác xóa';
        $('#nutXoa').addClass("displaynone");
    } else {
        document.getElementById('noiDungXoa').innerHTML = 'Thao tác này sẽ xóa hãng sản xuất ' + tenHangXoa +
            ' vĩnh viễn và không thể khôi phục lại, nên cân nhắc trước khi xóa';
        document.getElementById('maHangXoa').value = maHangXoa;
        $('#nutXoa').removeClass("displaynone");
    }
};

function xoaMaGiamGia(maGiamGiaXoa, soDonDaApDung) {
    if (soDonDaApDung > 0) {
        document.getElementById('noiDungXoa').innerHTML = 'Mã giảm giá' + maGiamGiaXoa +
            ' đã được áp dụng cho ' + soDonDaApDung +
            ' PHIẾU XUẤT nên không thể tiến hành thao tác xóa';
        $('#nutXoa').addClass("displaynone");
    } else {
        document.getElementById('noiDungXoa').innerHTML = 'Thao tác này sẽ xóa mã giảm giá ' + maGiamGiaXoa +
            ' vĩnh viễn và không thể khôi phục lại, nên cân nhắc trước khi xóa';
        document.getElementById('maGiamGiaXoa').value = maGiamGiaXoa;
        $('#nutXoa').removeClass("displaynone");
    }
};

function formatGia(input) {
    input.value = parseFloat(input.value.replace(/,/g,
            ""))
        .toFixed(0)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

function capNhatGia(tenSanPhamXoa, maSanPhamXoa, giaNhapSua, giaBanSua, giaKhuyenMaiSua) {
    document.getElementById('noiDungSuaGia').innerHTML = 'Nhập giá của [' + tenSanPhamXoa +
        '] theo mẫu bên dưới';
    document.getElementById('maSanPhamSuaGia').value = maSanPhamXoa;
    document.getElementById('giaNhap').value = giaNhapSua;
    formatGia(document.getElementById('giaNhap'));
    document.getElementById('giaNhapHien').innerHTML = document.getElementById('giaNhap').value;
    document.getElementById('giaBan').value = giaBanSua;
    formatGia(document.getElementById('giaBan'));
    if (giaKhuyenMaiSua > 0) {
        document.getElementById('giaKhuyenMaiCheck').checked = true;
        document.getElementById('giaKhuyenMai').required = true;
        document.getElementById('giaKhuyenMai').value = giaKhuyenMaiSua;
        formatGia(document.getElementById('giaKhuyenMai'));
        $('#noiDungGiaKhuyenMai').removeClass("displaynone");
        $('#giaKhuyenMai').removeClass("displaynone");
    } else {
        document.getElementById('giaKhuyenMaiCheck').checked = false;
        document.getElementById('giaKhuyenMai').required = false;
        document.getElementById('giaKhuyenMai').value = giaBanSua * 99 / 100;
        formatGia(document.getElementById('giaKhuyenMai'));
        $('#noiDungGiaKhuyenMai').addClass("displaynone");
        $('#giaKhuyenMai').addClass("displaynone");
    }
};

function hienThiGiaKhuyenMai() {
    var giaKhuyenMaiCheck = document.getElementById('giaKhuyenMaiCheck');
    var giaKhuyenMai = document.getElementById('giaKhuyenMai');
    if (giaKhuyenMaiCheck.checked) {
        $('#noiDungGiaKhuyenMai').removeClass("displaynone");
        $('#giaKhuyenMai').removeClass("displaynone");
    } else {
        $('#noiDungGiaKhuyenMai').addClass("displaynone");
        $('#giaKhuyenMai').addClass("displaynone");
    }
    giaKhuyenMai.required = giaKhuyenMaiCheck.checked;
};

function dinhDangGia(input) {
    var giaTri = input.value.split(","); // format tien ,,, lai thanh so
    var temp = "";
    for (var i = 0; i < giaTri.length; i++) {
        temp += giaTri[i];
    }
    input.value = parseFloat(temp);
    if (isNaN(input.value)) {
        input.value = 0;
    } else {
        formatGia(input);
    }
};

function doiTen(tenHang) {
    tenHang.value = tenHang.value.toUpperCase();
};

function chinhNgay(inputNgayBatDau, idNgayKetThuc) {
    var inputNgayKetThuc = document.getElementById(idNgayKetThuc);
    inputNgayKetThuc.value = inputNgayBatDau.value;
    inputNgayKetThuc.min = inputNgayBatDau.value;
};
$(document).ready(function() {
    // Add Row
    $('#add-row').DataTable({
        "pageLength": 10,
        order: [[0, "desc"]]
    });
});

