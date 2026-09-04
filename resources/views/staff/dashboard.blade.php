@extends('layouts.staff')

@section('title', 'Bảng điều khiển Staff')


@push('styles')

<style>

    .staff-dashboard-header {
        margin-bottom: 28px;
    }

    .staff-dashboard-header h1 {
        margin: 0 0 7px;
        font-size: 30px;
    }

    .staff-dashboard-header p {
        margin: 0;
        color: #78837b;
    }



    .staff-welcome-card {
        background: #ffffff;
        border: 1px solid #e2e8e3;
        border-radius: 16px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(31, 53, 39, 0.05);
    }

    .staff-welcome-card h2 {
        margin: 0 0 8px;
        font-size: 23px;
    }

    .staff-welcome-card p {
        margin: 0;
        color: #778178;
    }



    .staff-dashboard-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }



    .staff-dashboard-card {
        min-height: 145px;

        display: flex;
        flex-direction: column;
        justify-content: space-between;

        background: #ffffff;
        border: 1px solid #e2e8e3;
        border-radius: 14px;

        padding: 20px;

        box-shadow: 0 4px 14px rgba(31, 53, 39, 0.04);
    }

    .staff-dashboard-card-icon {
        font-size: 24px;
        color: #327049;
    }

    .staff-dashboard-card h3 {
        margin: 14px 0 5px;
        font-size: 16px;
    }

    .staff-dashboard-card p {
        margin: 0;
        color: #858e87;
        font-size: 13px;
        line-height: 1.5;
    }



    .staff-dashboard-note {
        margin-top: 25px;

        background: #edf3ef;
        border-radius: 12px;

        padding: 16px 18px;

        font-size: 13px;
        color: #627068;

        line-height: 1.6;
    }



    @media (max-width: 1100px) {

        .staff-dashboard-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }


    @media (max-width: 650px) {

        .staff-dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

</style>

@endpush


@section('content')


<div class="staff-dashboard-header">

    <h1>
        Bảng điều khiển
    </h1>

    <p>
        Khu vực làm việc dành cho nhân viên MiniGrove.
    </p>

</div>



<div class="staff-welcome-card">

    <h2>
        Xin chào,
        {{ auth()->user()->name }}
    </h2>

    <p>
        Bạn đang đăng nhập với vai trò Staff.
    </p>

</div>



<div class="staff-dashboard-grid">


    <div class="staff-dashboard-card">

        <div class="staff-dashboard-card-icon">
            ▧
        </div>

        <div>

            <h3>
                Đơn hàng
            </h3>

            <p>
                Theo dõi và xử lý các đơn hàng
                theo quyền được phân công.
            </p>

        </div>

    </div>



    <div class="staff-dashboard-card">

        <div class="staff-dashboard-card-icon">
            ↩
        </div>

        <div>

            <h3>
                Trả hàng / Hoàn tiền
            </h3>

            <p>
                Theo dõi các yêu cầu trả hàng
                và hoàn tiền cần xử lý.
            </p>

        </div>

    </div>



    <div class="staff-dashboard-card">

        <div class="staff-dashboard-card-icon">
            ◌
        </div>

        <div>

            <h3>
                Chat
            </h3>

            <p>
                Hỗ trợ Customer thông qua
                hệ thống trò chuyện MiniGrove.
            </p>

        </div>

    </div>



    <div class="staff-dashboard-card">

        <div class="staff-dashboard-card-icon">
            ☆
        </div>

        <div>

            <h3>
                Đánh giá sản phẩm
            </h3>

            <p>
                Theo dõi và phản hồi đánh giá
                sản phẩm khi được phép.
            </p>

        </div>

    </div>


</div>



<div class="staff-dashboard-note">

    Staff chỉ được truy cập các chức năng nghiệp vụ
    được hệ thống cấp quyền.

    Thông tin cá nhân và thông tin nhận hàng của Customer
    không được hiển thị trong khu vực Staff.

</div>


@endsection