@if (!empty($danhSachSanPhamHienThi))
    @foreach ($danhSachSanPhamHienThi as $sanPham)
        <!-- single-product-wrap start -->
        <div class="col-lg-12">
            <div class="single-product-wrap">
                <div class="product-image">
                    <a href="{{ url('chitietsp?masp=' . $sanPham->id_products) }}">
                        @if (!empty($danhSachThuVienHinh))
                            @foreach ($danhSachThuVienHinh as $thuVienHinh)
                                @if ($thuVienHinh->id_photo == $sanPham->id_photo)
                                    <img src="{{ asset('img/sanpham/' . $thuVienHinh->photo_1) }}"
                                        alt="Li's Product Image">
                                @endif
                            @endforeach
                        @endif
                    </a>
                    @if (!empty($danhSachLaptop))
                        @foreach ($danhSachLaptop as $laptop)
                            @if ($laptop->id_laptop == $sanPham->id_laptop)
                                @if ($laptop->status == 0)
                                    <span class="sticker tinhtrang">Mới</span>
                                @else
                                    <span class="sticker tinhtrang tinhtrangcu">Cũ</span>
                                @endif
                            @endif
                        @endforeach
                    @endif
                </div>
                <div class="product_desc">
                    <div class="product_desc_info">
                        <div class="product-review">
                            <h5 class="manufacturer">
                                <a class="mauchu-link" href="{{ url('danhsachsp?loaisp=' . $sanPham->cat_products) }}">
                                    @if ($sanPham->cat_products == 0)
                                        Điện Thoại
                                    @else
                                        Phụ Kiện
                                    @endif
                                </a>
                            </h5>
                        </div>
                        <h4><a class="product_name"
                                href="{{ url('chitietsp?masp=' . $sanPham->id_products) }}">{{ $sanPham->name_products }}</a>
                        </h4>
                        <h5 class="manufacturer">SP{{ $sanPham->id_products }}</h5>
                        {{-- @if (!empty($danhSachHangSanXuat))  // thong tin ben ngoai
                                                                @foreach ($danhSachHangSanXuat as $hangSanXuat)
                                                                    @if ($hangSanXuat->id_mfg == $sanPham->id_mfg)
                                                                        <div>Hãng: {{$hangSanXuat->name_mfg}}</div>
                                                                    @endif
                                                                @endforeach
                                                                @endif
                                                                @if (!empty($danhSachLaptop))
                                                                @foreach ($danhSachLaptop as $laptop)
                                                                    @if ($laptop->id_laptop == $sanPham->id_laptop)
                                                                        <div>CPU: {{$laptop->cpu}}</div>
                                                                        <div>RAM: {{$laptop->ram}} GB</div>
                                                                        <div>Card đồ hoạ: @if ($laptop->card_laptop == 0)
                                                                            Onboard
                                                                        @elseif (($laptop->card_laptop == 1))
                                                                            Nvidia
                                                                        @else
                                                                            Amd
                                                                        @endif</div>
                                                                        <div>Ổ cứng: {{$laptop->disk_laptop}} GB</div>
                                                                        <div>Màn hình: {{$laptop->screen}} inch</div>
                                                                    @endif
                                                                @endforeach
                                                                @endif --}}
                        <div class="price-box mt-0">
                            @if (!empty($sanPham->promotional_price))
                                <p class="new-price giakhuyenmai">
                                    {{ number_format($sanPham->promotional_price, 0, ',') }}đ</p>
                                <p class="new-price giaban">
                                    {{ number_format($sanPham->sale_price, 0, ',') }}đ</p>
                            @else
                                @if ($sanPham->sale_price > 0)
                                    <p class="new-price giakhuyenmai">
                                        {{ number_format($sanPham->sale_price, 0, ',') }}đ</p>
                                @else
                                    <p class="new-price giakhuyenmai">
                                        Liên hệ</p>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="add-actions">
                        <ul class="ulthemgiohang pt-0 mt-0">
                            <li>
                                <form action="{{ route('xulygiohang') }}" method="post">
                                    <button class="btn btn-focus p-1 pr-2 pl-2" type="submit" name="thaoTac"
                                        value="thêm giỏ hàng">thêm giỏ hàng</button>
                                    <input hidden value="1" type="number" name="soLuongMua" min="1"
                                        max="1" required>
                                    <input hidden value="{{ $sanPham->id_products }}" type="number"
                                        name="maSanPhamMua" required>
                                    @error('maSanPhamMua')
                                        <div style="color: red;font-size:10px;display:inline-block;width:100%">
                                            {{ $message }}</div>
                                    @enderror
                                    @error('soLuongMua')
                                        <div style="color: red;font-size:10px;display:inline-block;width:100%">
                                            {{ $message }}</div>
                                    @enderror
                                    @csrf
                                </form>
                            </li>
                            {{-- <li><a href="{{ url('chitietsp?masp=' . $sanPham->id_products) }}" title="quick view"
                                                                    class="quick-view-btn" data-toggle="modal"
                                                                    data-target="#exampleModalCenter"><i
                                                                        class="fa fa-eye"></i></a></li> --}}
                            <li class="ml-1">
                                @php
                                    $flag = false;
                                    if (!empty(session('yeuThich'))) {
                                        foreach (session('yeuThich') as $ctyt) {
                                            if ($ctyt['id_products'] == $sanPham->id_products) {
                                                $flag = true;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                @if ($flag)
                                    <a class="links-details btn btn-focus p-1 pr-2 pl-2"
                                        href="{{ url('xulyyeuthich?thaotac=yeuthich&masp=' . $sanPham->id_products) }}"><i
                                            class="fa fa-heart"></i></a>
                                @else
                                    <a class="links-details btn btn-focus p-1 pr-2 pl-2"
                                        href="{{ url('xulyyeuthich?thaotac=yeuthich&masp=' . $sanPham->id_products) }}"><i
                                            class="fa fa-heart-o"></i></a>
                                @endif
                            </li>
                            <li class="ml-1"><a class="links-details btn btn-focus p-1 pr-2 pl-2"
                                    href="{{ url('chitietsp?masp=' . $sanPham->id_products) }}"><i
                                        class="fa fa-eye"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- single-product-wrap end -->
    @endforeach
@endif
