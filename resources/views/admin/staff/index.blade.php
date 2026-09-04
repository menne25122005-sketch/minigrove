@extends('layouts.admin')

@section('title', 'Quản lý nhân viên')


@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/admin-staff.css') }}"
    >

@endpush


@section('content')

<div class="staff-page">


    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="staff-header">

        <div>

            <h1>
                Nhân viên
            </h1>

            <p>
                Quản lý tài khoản Staff, thông tin công việc
                và trạng thái hoạt động.
            </p>

        </div>


        <a
            href="{{ route('admin.staff.create') }}"
            class="staff-btn staff-btn-primary"
        >
            + Thêm nhân viên
        </a>

    </div>



    {{-- =========================================================
         THỐNG KÊ
    ========================================================== --}}
    <div class="staff-stats">


        <div class="staff-stat-card">

            <div class="staff-stat-title">
                Tổng nhân viên
            </div>

            <div class="staff-stat-number">
                {{ $totalStaff }}
            </div>

        </div>


        <div class="staff-stat-card">

            <div class="staff-stat-title">
                Active
            </div>

            <div class="staff-stat-number">
                {{ $activeStaff }}
            </div>

        </div>


        <div class="staff-stat-card">

            <div class="staff-stat-title">
                Inactive
            </div>

            <div class="staff-stat-number">
                {{ $inactiveStaff }}
            </div>

        </div>


    </div>



    {{-- =========================================================
         DANH SÁCH NHÂN VIÊN
    ========================================================== --}}
    <div class="staff-section">


        <div class="staff-section-heading">

            <div>

                <h2>
                    Danh sách nhân viên
                </h2>

                <p>
                    Quản lý tài khoản và trạng thái làm việc
                    của nhân viên MiniGrove.
                </p>

            </div>

        </div>



        {{-- =====================================================
             FILTER
        ====================================================== --}}
        <form
            method="GET"
            action="{{ route('admin.staff.index') }}"
            class="staff-filter"
        >

            <input
                type="text"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="Tên, email, SĐT hoặc chức vụ..."
                autocomplete="off"
            >


            <select name="status">

                <option value="">
                    Tất cả trạng thái
                </option>


                <option
                    value="active"
                    {{ $status === 'active' ? 'selected' : '' }}
                >
                    Active
                </option>


                <option
                    value="inactive"
                    {{ $status === 'inactive' ? 'selected' : '' }}
                >
                    Inactive
                </option>

            </select>


            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Lọc
            </button>


            <a
                href="{{ route('admin.staff.index') }}"
                class="staff-btn staff-btn-secondary"
            >
                Đặt lại
            </a>

        </form>



        {{-- =====================================================
             TABLE
        ====================================================== --}}
        <div class="staff-table-wrapper">

            <table class="staff-table">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Nhân viên
                        </th>

                        <th>
                            Liên hệ
                        </th>

                        <th>
                            Chức vụ
                        </th>

                        <th>
                            Ngày tạo
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($staffMembers as $staff)

                        <tr>


                            {{-- ID --}}
                            <td>

                                <strong>
                                    #{{ $staff->id }}
                                </strong>

                            </td>



                            {{-- NHÂN VIÊN --}}
                            <td>

                                <div class="staff-person">

                                    <div class="staff-avatar">

                                        {{
                                            mb_strtoupper(
                                                mb_substr(
                                                    $staff->name,
                                                    0,
                                                    1
                                                )
                                            )
                                        }}

                                    </div>


                                    <div>

                                        <strong>
                                            {{ $staff->name }}
                                        </strong>

                                        <small>
                                            Staff #{{ $staff->id }}
                                        </small>

                                    </div>

                                </div>

                            </td>



                            {{-- LIÊN HỆ --}}
                            <td>

                                <div class="staff-contact">

                                    <strong>
                                        {{ $staff->email }}
                                    </strong>

                                    <small>
                                        {{ $staff->phone ?? 'Chưa có SĐT' }}
                                    </small>

                                </div>

                            </td>



                            {{-- POSITION --}}
                            <td>

                                {{
                                    $staff->position
                                    ?: 'Chưa cập nhật'
                                }}

                            </td>



                            {{-- CREATED AT --}}
                            <td>

                                {{
                                    optional(
                                        $staff->created_at
                                    )->format(
                                        'd/m/Y H:i'
                                    )
                                }}

                            </td>



                            {{-- STATUS --}}
                            <td>

                                @if ($staff->is_active)

                                    <span
                                        class="
                                            staff-status
                                            staff-status-active
                                        "
                                    >
                                        Active
                                    </span>

                                @else

                                    <span
                                        class="
                                            staff-status
                                            staff-status-inactive
                                        "
                                    >
                                        Inactive
                                    </span>

                                @endif

                            </td>



                            {{-- ACTIONS --}}
                            <td>

                                <div class="staff-actions">


                                    <a
                                        href="{{
                                            route(
                                                'admin.staff.edit',
                                                $staff->id
                                            )
                                        }}"
                                        class="
                                            staff-btn
                                            staff-btn-small
                                            staff-btn-secondary
                                        "
                                    >
                                        Sửa
                                    </a>



                                    <form
                                        method="POST"
                                        action="{{
                                            route(
                                                'admin.staff.toggle-status',
                                                $staff->id
                                            )
                                        }}"
                                        onsubmit="
                                            return confirm(
                                                '{{ $staff->is_active
                                                    ? 'Bạn có chắc muốn chuyển nhân viên này sang Inactive?'
                                                    : 'Bạn có chắc muốn chuyển nhân viên này sang Active?' }}'
                                            );
                                        "
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <button
                                            type="submit"
                                            class="
                                                staff-btn
                                                staff-btn-small

                                                {{
                                                    $staff->is_active
                                                        ? 'staff-btn-inactive'
                                                        : 'staff-btn-active'
                                                }}
                                            "
                                        >

                                            {{
                                                $staff->is_active
                                                    ? 'Inactive'
                                                    : 'Active'
                                            }}

                                        </button>

                                    </form>


                                </div>

                            </td>


                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="staff-empty"
                            >
                                Không có nhân viên phù hợp.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        {{-- =====================================================
             PAGINATION
        ====================================================== --}}
        @if ($staffMembers->hasPages())

            <div class="staff-pagination">

                {{ $staffMembers->links() }}

            </div>

        @endif


    </div>


</div>

@endsection