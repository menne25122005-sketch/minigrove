@extends('layouts.admin')

@section('title', 'Quản lý tồn kho - MiniGrove')

@section('page-title', 'Quản lý tồn kho')


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/admin-stocks.css') }}"
>

@endpush



@section('content')


<div class="stock-page">


    {{-- =========================================================
         TIÊU ĐỀ
    ========================================================== --}}

    <div class="stock-heading">

        <div>

            <h1>
                Quản lý tồn kho
            </h1>


            <p>
                Theo dõi và điều chỉnh tồn kho sản phẩm.
            </p>


        </div>


    </div>





    {{-- =========================================================
         VALIDATION ERROR
    ========================================================== --}}


    @if ($errors->any())


        <div class="alert alert-danger">


            <strong>
                Không thể thực hiện thao tác:
            </strong>


            <ul class="stock-error-list">


                @foreach ($errors->all() as $error)


                    <li>
                        {{ $error }}
                    </li>


                @endforeach


            </ul>


        </div>


    @endif





    {{-- =========================================================
         THỐNG KÊ NHANH
    ========================================================== --}}


    <div class="stock-stats">


        <div class="stock-stat-card">


            <div class="stock-stat-label">
                Sản phẩm
            </div>


            <div class="stock-stat-value">

                {{ $productCount }}

            </div>


        </div>





        <div class="stock-stat-card stock-stat-warning">


            <div class="stock-stat-label">
                Sắp hết hàng
            </div>


            <div class="stock-stat-value">

                {{ $lowStockCount }}

            </div>


        </div>





        <div class="stock-stat-card stock-stat-danger">


            <div class="stock-stat-label">
                Hết hàng
            </div>


            <div class="stock-stat-value">

                {{ $outOfStockCount }}

            </div>


        </div>



    </div>






    {{-- =========================================================
         BỘ LỌC
    ========================================================== --}}


    <div class="card stock-filter-card">


        <form

            action="{{ route('admin.stocks.index') }}"

            method="GET"

            class="stock-filter-form"

        >



            <div class="stock-filter-field">


                <label for="keyword">
                    Tìm kiếm
                </label>


                <input

                    type="text"

                    id="keyword"

                    name="keyword"

                    value="{{ $keyword }}"

                    placeholder="Tên hoặc SKU..."

                >


            </div>






            <div class="stock-filter-field">


                <label for="status">
                    Trạng thái
                </label>


                <select

                    id="status"

                    name="status"

                >


                    <option

                        value=""

                        @selected($status === '')

                    >

                        Tất cả trạng thái

                    </option>




                    <option

                        value="in_stock"

                        @selected($status === 'in_stock')

                    >

                        Còn hàng

                    </option>




                    <option

                        value="low_stock"

                        @selected($status === 'low_stock')

                    >

                        Sắp hết hàng

                    </option>




                    <option

                        value="out_of_stock"

                        @selected($status === 'out_of_stock')

                    >

                        Hết hàng

                    </option>



                </select>


            </div>






            <div class="stock-filter-actions">


                <button

                    type="submit"

                    class="btn btn-primary"

                >

                    Lọc

                </button>





                <a

                    href="{{ route('admin.stocks.index') }}"

                    class="btn btn-secondary"

                >

                    Đặt lại

                </a>


            </div>



        </form>


    </div>





    {{-- =========================================================
         DANH SÁCH TỒN KHO
    ========================================================== --}}


    <div class="card">


        <div class="stock-table-header">


            <div>


                <h2>
                    Danh sách tồn kho
                </h2>


                <p>

                    Tổng {{ $stocks->total() }} mục

                </p>


            </div>


        </div>





        <div class="table-wrapper">


            <table class="stock-table">


                <thead>


                    <tr>

                        <th>
                            SKU
                        </th>


                        <th>
                            Tên
                        </th>


                        <th>
                            Tồn kho
                        </th>


                        <th>
                            Ngưỡng cảnh báo
                        </th>


                        <th>
                            Trạng thái
                        </th>


                        <th>
                            Hoạt động
                        </th>


                        <th>
                            Thao tác
                        </th>


                    </tr>


                </thead>



                <tbody>



                @forelse ($stocks as $stock)


                    @php


                        $quantity =
                            (int) $stock->stock_quantity;



                        $threshold =
                            (int) $stock->low_stock_threshold;



                        if ($quantity === 0) {


                            $stockStatus = 'out';


                            $stockStatusLabel =
                                'Hết hàng';


                        } elseif ($quantity <= $threshold) {


                            $stockStatus = 'low';


                            $stockStatusLabel =
                                'Sắp hết';


                        } else {


                            $stockStatus = 'normal';


                            $stockStatusLabel =
                                'Còn hàng';


                        }


                    @endphp



                    <tr>


                        <td>

                            <strong>

                                {{ $stock->sku }}

                            </strong>

                        </td>



                        <td>


                            <div class="stock-item-name">

                                {{ $stock->name }}

                            </div>


                            <div class="stock-item-unit">

                                Đơn vị:
                                Sản phẩm


                            </div>


                        </td>



                        <td>

                            <strong class="stock-quantity">

                                {{ number_format($quantity,0,',','.') }}

                            </strong>


                        </td>



                        <td>


                            <form

                                action="{{ route(
                                    'admin.stocks.threshold.update',
                                    [
                                        'stockType'=>'PRODUCT',
                                        'itemId'=>$stock->item_id
                                    ]
                                ) }}"

                                method="POST"

                                class="stock-threshold-form"

                            >

                                @csrf

                                @method('PATCH')



                                <input

                                    type="number"

                                    name="low_stock_threshold"

                                    value="{{ $threshold }}"

                                    min="0"

                                    required

                                >



                                <button

                                    type="submit"

                                    class="stock-small-button"

                                >

                                    Lưu

                                </button>


                            </form>


                        </td>



                        <td>

                            <span class="
                                stock-status
                                stock-status-{{ $stockStatus }}
                            ">


                                {{ $stockStatusLabel }}


                            </span>


                        </td>
                                                </td>




                        <td>


                            @if($stock->is_active)


                                <span class="stock-active">

                                    Đang bán

                                </span>


                            @else


                                <span class="stock-inactive">

                                    Ngừng bán

                                </span>


                            @endif


                        </td>






                        <td>


                            <div class="stock-actions">



                                <button

                                    type="button"

                                    class="btn btn-sm btn-success"

                                    onclick="openStockModal(
                                        'PRODUCT',
                                        '{{ $stock->item_id }}',
                                        '{{ $stock->name }}',
                                        'import'
                                    )"

                                >

                                    Nhập

                                </button>





                                <button

                                    type="button"

                                    class="btn btn-sm btn-warning"

                                    onclick="openStockModal(
                                        'PRODUCT',
                                        '{{ $stock->item_id }}',
                                        '{{ $stock->name }}',
                                        'export'
                                    )"

                                >

                                    Xuất

                                </button>





                                <button

                                    type="button"

                                    class="btn btn-sm btn-primary"

                                    onclick="openStockModal(
                                        'PRODUCT',
                                        '{{ $stock->item_id }}',
                                        '{{ $stock->name }}',
                                        'adjust'
                                    )"

                                >

                                    Điều chỉnh

                                </button>





                                <a

                                    href="{{ route(
                                        'admin.stocks.history',
                                        [
                                            'stockType'=>'PRODUCT',
                                            'itemId'=>$stock->item_id
                                        ]
                                    ) }}"

                                    class="btn btn-sm btn-secondary"

                                >

                                    Lịch sử

                                </a>



                            </div>


                        </td>


                    </tr>



                @empty


                    <tr>


                        <td

                            colspan="7"

                            class="empty-row"

                        >


                            Không có dữ liệu tồn kho.


                        </td>


                    </tr>


                @endforelse



                </tbody>


            </table>


        </div>





        <div class="stock-pagination">


            {{ $stocks->links() }}


        </div>


    </div>





</div>







{{-- =========================================================
     MODAL THAY ĐỔI TỒN KHO
========================================================== --}}



<div

    id="stockModal"

    class="stock-modal"

    style="display:none"

>



    <div class="stock-modal-box">



        <div class="stock-modal-header">


            <h3>

                Điều chỉnh tồn kho

            </h3>


            <button

                type="button"

                onclick="closeStockModal()"

                class="stock-modal-close"

            >

                ×

            </button>


        </div>





        <form

            id="stockChangeForm"

            method="POST"

        >


            @csrf





            <div class="form-group">


                <label>

                    Sản phẩm

                </label>


                <input

                    type="text"

                    id="stockItemName"

                    readonly

                >


            </div>





            <div class="form-group">


                <label>

                    Số lượng

                </label>


                <input

                    type="number"

                    name="quantity"

                    min="0"

                    required

                >


            </div>





            <div class="form-group">


                <label>

                    Ghi chú

                </label>


                <textarea

                    name="note"

                    rows="3"

                    required

                ></textarea>


            </div>




            <input

                type="hidden"

                name="operation"

                id="stockOperation"

            >






            <div class="stock-modal-actions">


                <button

                    type="button"

                    onclick="closeStockModal()"

                    class="btn btn-secondary"

                >

                    Hủy

                </button>





                <button

                    type="submit"

                    class="btn btn-primary"

                >

                    Xác nhận

                </button>


            </div>




        </form>



    </div>



</div>







@push('scripts')


<script>


function openStockModal(
    type,
    id,
    name,
    operation
)
{


    const modal =
        document.getElementById(
            'stockModal'
        );


    const form =
        document.getElementById(
            'stockChangeForm'
        );



    document.getElementById(
        'stockItemName'
    ).value = name;



    document.getElementById(
        'stockOperation'
    ).value = operation;





    form.action =
        `/admin/stocks/${type}/${id}/change`;



    modal.style.display =
        'flex';



}





function closeStockModal()
{

    document.getElementById(
        'stockModal'
    ).style.display =
        'none';

}



</script>


@endpush



@endsection