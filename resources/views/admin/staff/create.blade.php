@extends('layouts.admin')

@section('title', 'Thêm nhân viên')


@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/admin-staff.css') }}"
    >

@endpush


@section('content')

<div class="staff-page">


    {{-- =========================================================
         BACK
    ========================================================== --}}
    <div>

        <a
            href="{{ route('admin.staff.index') }}"
            class="staff-btn staff-btn-secondary"
        >
            ← Quay lại Nhân viên
        </a>

    </div>



    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="staff-header">

        <div>

            <h1>
                Thêm nhân viên
            </h1>

            <p>
                Tạo tài khoản Staff mới cho hệ thống MiniGrove.
            </p>

        </div>

    </div>



    {{-- =========================================================
         FORM
    ========================================================== --}}
    <div class="staff-form-card">


        <form
            method="POST"
            action="{{ route('admin.staff.store') }}"
        >

            @csrf



            <div class="staff-form-grid">


                {{-- =================================================
                     HỌ TÊN
                ================================================== --}}
                <div class="staff-form-group">

                    <label for="name">
                        Họ tên
                        <span class="staff-required">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Nhập họ tên nhân viên"
                        autocomplete="name"
                    >

                    @error('name')

                        <div class="staff-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                {{-- =================================================
                     EMAIL
                ================================================== --}}
                <div class="staff-form-group">

                    <label for="email">
                        Email
                        <span class="staff-required">*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Ví dụ: staff@minigrove.com"
                        autocomplete="email"
                    >

                    @error('email')

                        <div class="staff-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                {{-- =================================================
                     SĐT
                ================================================== --}}
                <div class="staff-form-group">

                    <label for="phone">
                        Số điện thoại
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="Ví dụ: 0912345678"
                        maxlength="10"
                        inputmode="numeric"
                        autocomplete="tel"
                    >

                    <small class="staff-form-hint">
                        Gồm 10 chữ số và bắt đầu bằng 0.
                    </small>

                    @error('phone')

                        <div class="staff-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                {{-- =================================================
                     CHỨC VỤ
                ================================================== --}}
                <div class="staff-form-group">

                    <label for="position">
                        Chức vụ
                    </label>

                    <input
                        type="text"
                        id="position"
                        name="position"
                        value="{{ old('position') }}"
                        placeholder="Ví dụ: Nhân viên vận hành"
                        maxlength="100"
                    >

                    @error('position')

                        <div class="staff-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                {{-- =================================================
                     PASSWORD
                ================================================== --}}
                <div class="staff-form-group">

                    <label for="password">
                        Mật khẩu
                        <span class="staff-required">*</span>
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Tối thiểu 8 ký tự"
                        autocomplete="new-password"
                    >

                    @error('password')

                        <div class="staff-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                {{-- =================================================
                     CONFIRM PASSWORD
                ================================================== --}}
                <div class="staff-form-group">

                    <label for="password_confirmation">
                        Xác nhận mật khẩu
                        <span class="staff-required">*</span>
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Nhập lại mật khẩu"
                        autocomplete="new-password"
                    >

                </div>


            </div>



            {{-- =====================================================
                 NOTE
            ====================================================== --}}
            <div class="staff-form-note">

                Tài khoản mới sẽ được tạo với vai trò
                <strong>Staff</strong>
                và trạng thái mặc định là
                <strong>Active</strong>.

            </div>



            {{-- =====================================================
                 ACTIONS
            ====================================================== --}}
            <div class="staff-form-actions">


                <a
                    href="{{ route('admin.staff.index') }}"
                    class="staff-btn staff-btn-secondary"
                >
                    Hủy
                </a>


                <button
                    type="submit"
                    class="staff-btn staff-btn-primary"
                >
                    Thêm nhân viên
                </button>


            </div>


        </form>


    </div>


</div>

@endsection