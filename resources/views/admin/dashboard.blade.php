@extends('layouts.admin')


@section('title', 'Dashboard - MiniGrove Admin')


@section('page-title', 'Dashboard')


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/admin-dashboard.css') }}"
>

@endpush



@section('content')


<div class="dashboard-page">


    {{-- HEADER --}}
    <div class="dashboard-header">

        <div>

            <h1>
                Tổng quan MiniGrove
            </h1>


            <p>
                Theo dõi nhanh tình trạng hoạt động của hệ thống.
            </p>

        </div>


    </div>




    {{-- STATISTICS --}}

    <div class="dashboard-stats">



        <div class="dashboard-stat-card">

            <div class="dashboard-stat-label">
                Doanh thu
            </div>

            <div class="dashboard-stat-value">

                {{ number_format($completedRevenue, 0, ',', '.') }}

                đ

            </div>

        </div>




        <div class="dashboard-stat-card">

            <div class="dashboard-stat-label">
                Đơn hàng
            </div>

            <div class="dashboard-stat-value">

                {{ $orderCount }}

            </div>

        </div>




        <div class="dashboard-stat-card">

            <div class="dashboard-stat-label">
                Khách hàng
            </div>

            <div class="dashboard-stat-value">

                {{ $customerCount }}

            </div>

        </div>


        <div class="dashboard-stat-card">

            <div class="dashboard-stat-label">
                Đơn đang xử lý
            </div>

            <div class="dashboard-stat-value">

                {{ $processingOrderCount }}

            </div>

        </div>




        <div class="dashboard-stat-card">

            <div class="dashboard-stat-label">
                Hoàn trả chờ xử lý
            </div>

            <div class="dashboard-stat-value">

                {{ $pendingReturnCount }}

            </div>

        </div>



    </div>






    {{-- RECENT ORDERS --}}

    <div class="dashboard-section">


        <div class="dashboard-section-header">

            <h2>
                Đơn hàng gần nhất
            </h2>

        </div>



        <div class="dashboard-table-wrapper">


            <table class="dashboard-table">


                <thead>

                    <tr>

                        <th>
                            Mã đơn
                        </th>


                        <th>
                            Customer
                        </th>


                        <th>
                            Tổng tiền
                        </th>


                        <th>
                            Thanh toán
                        </th>


                        <th>
                            Trạng thái
                        </th>


                    </tr>


                </thead>



                <tbody>


                @forelse($recentOrders as $order)


                    <tr>


                        <td>

                            {{ $order->order_code }}

                        </td>


                        <td>

                            Customer #{{ $order->user_id }}

                        </td>


                        <td>

                            {{ number_format($order->total_amount,0,',','.') }}

                            đ

                        </td>


                        <td>

                            {{ $order->payment_status }}

                        </td>


                        <td>

                            <span class="dashboard-status">

                                {{ $order->order_status }}

                            </span>

                        </td>


                    </tr>



                @empty


                    <tr>

                        <td colspan="5">

                            Chưa có đơn hàng.

                        </td>

                    </tr>


                @endforelse



                </tbody>


            </table>


        </div>


    </div>







    <div class="dashboard-two-column">



        {{-- TOP PRODUCTS --}}

        <div class="dashboard-section">


            <h2>
                Sản phẩm bán chạy
            </h2>



            <table class="dashboard-table">


                <thead>

                    <tr>

                        <th>
                            Sản phẩm
                        </th>


                        <th>
                            Đã bán
                        </th>


                        <th>
                            Doanh thu
                        </th>

                    </tr>


                </thead>



                <tbody>



                @forelse($topProducts as $product)


                    <tr>

                        <td>
                            {{ $product->item_name }}
                        </td>


                        <td>
                            {{ $product->total_sold }}
                        </td>


                        <td>

                            {{ number_format($product->total_revenue,0,',','.') }}

                            đ

                        </td>


                    </tr>


                @empty


                    <tr>

                        <td colspan="3">
                            Chưa có dữ liệu.
                        </td>

                    </tr>


                @endforelse



                </tbody>


            </table>


        </div>







        {{-- LOW STOCK --}}

        <div class="dashboard-section">


            <h2>
                Sản phẩm sắp hết hàng
            </h2>



            <table class="dashboard-table">


                <thead>

                    <tr>

                        <th>
                            Sản phẩm
                        </th>


                        <th>
                            Tồn kho
                        </th>


                    </tr>

                </thead>



                <tbody>



                @forelse($lowStockProducts as $product)


                    <tr>

                        <td>

                            {{ $product->name }}

                        </td>


                        <td>

                            {{ $product->stock_quantity }}

                        </td>


                    </tr>


                @empty


                    <tr>

                        <td colspan="2">

                            Kho đang ổn định.

                        </td>

                    </tr>


                @endforelse



                </tbody>


            </table>



        </div>



    </div>







    {{-- QUICK LINKS --}}


    <div class="dashboard-section">


        <h2>
            Truy cập nhanh
        </h2>



        <div class="dashboard-links">


            <a href="{{ route('admin.categories.index') }}">

                Quản lý danh mục

            </a>



            <a href="{{ route('admin.products.index') }}">

                Quản lý sản phẩm

            </a>



            <a href="{{ route('admin.orders.index') }}">

                Quản lý đơn hàng

            </a>



            <a href="{{ route('admin.customers.index') }}">

                Quản lý khách hàng

            </a>






            <a href="{{ route('admin.groves.index') }}">

                Quản lý Grove

            </a>



        </div>


    </div>



</div>



@endsection