@extends('layouts.admin')

@section('title', 'Sửa nhân viên')


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
                Sửa nhân viên
            </h1>

            <p>
                Cập nhật thông tin tài khoản Staff #{{ $staffMember->id }}.
            </p>

        </div>

    </div>



    {{-- =========================================================
         FORM
    ========================================================== --}}
    <div class="staff-form-card">

        <form
            method="POST"
            action="{{ route('admin.staff.update', $staffMember->id) }}"
        >

            @csrf
            @method('PUT')


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
                        value="{{ old('name', $staffMember->name) }}"
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
                        value="{{ old('email', $staffMember->email) }}"
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
                        value="{{ old('phone', $staffMember->phone) }}"
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
                        value="{{ old('position', $staffMember->position) }}"
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
                     PASSWORD MỚI
                ================================================== --}}
                <div class="staff-form-group">

                    <label for="password">
                        Mật khẩu mới
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Để trống nếu không muốn đổi"
                        autocomplete="new-password"
                    >

                    <small class="staff-form-hint">
                        Chỉ nhập khi muốn thay đổi mật khẩu.
                        Tối thiểu 8 ký tự.
                    </small>

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
                        Xác nhận mật khẩu mới
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Nhập lại mật khẩu mới"
                        autocomplete="new-password"
                    >

                </div>


            </div>



            {{-- =====================================================
                 NOTE
            ====================================================== --}}
            <div class="staff-form-note">

                Nếu không muốn thay đổi mật khẩu,
                hãy để trống cả hai ô mật khẩu.

                Trạng thái hiện tại:
                <strong>
                    {{ $staffMember->is_active ? 'Active' : 'Inactive' }}
                </strong>.

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
                    Cập nhật nhân viên
                </button>

            </div>


        </form>

    </div>


</div>

@endsection