<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffController extends Controller
{
    /**
     * Danh sách Nhân viên.
     */
    public function index(Request $request): View
    {
        $keyword = trim(
            (string) $request->query('keyword', '')
        );

        $status = trim(
            (string) $request->query('status', '')
        );


        /*
        |--------------------------------------------------------------------------
        | STAFF QUERY
        |--------------------------------------------------------------------------
        */
        $query = User::query()
            ->select([
                'id',
                'role_id',
                'name',
                'email',
                'phone',
                'position',
                'is_active',
                'created_at',
            ])
            ->whereHas(
                'role',
                function ($roleQuery) {
                    $roleQuery->where(
                        'name',
                        'staff'
                    );
                }
            )
            ->with([
                'role:id,name,display_name',
            ])
            ->latest('id');


        /*
        |--------------------------------------------------------------------------
        | TÌM KIẾM
        |--------------------------------------------------------------------------
        */
        if ($keyword !== '') {
            $query->where(
                function ($searchQuery) use ($keyword) {
                    $searchQuery
                        ->where(
                            'name',
                            'like',
                            '%' . $keyword . '%'
                        )
                        ->orWhere(
                            'email',
                            'like',
                            '%' . $keyword . '%'
                        )
                        ->orWhere(
                            'phone',
                            'like',
                            '%' . $keyword . '%'
                        )
                        ->orWhere(
                            'position',
                            'like',
                            '%' . $keyword . '%'
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LỌC TRẠNG THÁI
        |--------------------------------------------------------------------------
        */
        if ($status === 'active') {
            $query->where(
                'is_active',
                true
            );

        } elseif ($status === 'inactive') {
            $query->where(
                'is_active',
                false
            );
        }


        $staffMembers = $query
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | THỐNG KÊ
        |--------------------------------------------------------------------------
        */
        $staffBaseQuery = User::query()
            ->whereHas(
                'role',
                function ($roleQuery) {
                    $roleQuery->where(
                        'name',
                        'staff'
                    );
                }
            );


        $totalStaff =
            (clone $staffBaseQuery)
                ->count();

        $activeStaff =
            (clone $staffBaseQuery)
                ->where(
                    'is_active',
                    true
                )
                ->count();

        $inactiveStaff =
            (clone $staffBaseQuery)
                ->where(
                    'is_active',
                    false
                )
                ->count();


        return view(
            'admin.staff.index',
            compact(
                'staffMembers',
                'keyword',
                'status',
                'totalStaff',
                'activeStaff',
                'inactiveStaff'
            )
        );
    }


    /**
     * Form thêm Nhân viên.
     */
    public function create(): View
    {
        return view(
            'admin.staff.create'
        );
    }


    /**
     * Lưu Nhân viên mới.
     */
    public function store(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'phone' => [
                    'nullable',
                    'regex:/^0[0-9]{9}$/',
                    'unique:users,phone',
                ],

                'position' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ],
            [
                'name.required' =>
                    'Vui lòng nhập họ tên nhân viên.',

                'email.required' =>
                    'Vui lòng nhập email.',

                'email.email' =>
                    'Email không đúng định dạng.',

                'email.unique' =>
                    'Email này đã được sử dụng.',

                'phone.regex' =>
                    'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',

                'phone.unique' =>
                    'Số điện thoại này đã được sử dụng.',

                'position.max' =>
                    'Chức vụ không được vượt quá 100 ký tự.',

                'password.required' =>
                    'Vui lòng nhập mật khẩu.',

                'password.min' =>
                    'Mật khẩu phải có ít nhất 8 ký tự.',

                'password.confirmed' =>
                    'Xác nhận mật khẩu không khớp.',
            ]
        );


        $staffRole = Role::query()
            ->where(
                'name',
                'staff'
            )
            ->firstOrFail();


        User::create([
            'role_id' =>
                $staffRole->id,

            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'phone' =>
                $validated['phone']
                ?? null,

            'position' =>
                $validated['position']
                ?? null,

            'is_active' =>
                true,

            /*
             * User model đã có cast:
             * 'password' => 'hashed'
             */
            'password' =>
                $validated['password'],
        ]);


        return redirect()
            ->route(
                'admin.staff.index'
            )
            ->with(
                'success',
                'Thêm nhân viên thành công.'
            );
    }


    /**
     * Form sửa Nhân viên.
     */
    public function edit(
        int $staff
    ): View {

        $staffMember =
            $this->findStaff($staff);


        return view(
            'admin.staff.edit',
            compact(
                'staffMember'
            )
        );
    }


    /**
     * Cập nhật Nhân viên.
     */
    public function update(
        Request $request,
        int $staff
    ): RedirectResponse {

        $staffMember =
            $this->findStaff($staff);


        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',

                    Rule::unique(
                        'users',
                        'email'
                    )->ignore(
                        $staffMember->id
                    ),
                ],

                'phone' => [
                    'nullable',
                    'regex:/^0[0-9]{9}$/',

                    Rule::unique(
                        'users',
                        'phone'
                    )->ignore(
                        $staffMember->id
                    ),
                ],

                'position' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ],
            [
                'name.required' =>
                    'Vui lòng nhập họ tên nhân viên.',

                'email.required' =>
                    'Vui lòng nhập email.',

                'email.email' =>
                    'Email không đúng định dạng.',

                'email.unique' =>
                    'Email này đã được sử dụng.',

                'phone.regex' =>
                    'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',

                'phone.unique' =>
                    'Số điện thoại này đã được sử dụng.',

                'position.max' =>
                    'Chức vụ không được vượt quá 100 ký tự.',

                'password.min' =>
                    'Mật khẩu mới phải có ít nhất 8 ký tự.',

                'password.confirmed' =>
                    'Xác nhận mật khẩu mới không khớp.',
            ]
        );


        $staffMember->name =
            $validated['name'];

        $staffMember->email =
            $validated['email'];

        $staffMember->phone =
            $validated['phone']
            ?? null;

        $staffMember->position =
            $validated['position']
            ?? null;


        /*
         * Chỉ đổi mật khẩu nếu Admin nhập mật khẩu mới.
         */
        if (
            !empty(
                $validated['password']
                ?? null
            )
        ) {
            $staffMember->password =
                $validated['password'];
        }


        $staffMember->save();


        return redirect()
            ->route(
                'admin.staff.index'
            )
            ->with(
                'success',
                'Cập nhật nhân viên thành công.'
            );
    }


    /**
     * Active / Inactive Nhân viên.
     */
    public function toggleStatus(
        int $staff
    ): RedirectResponse {

        $staffMember =
            $this->findStaff($staff);


        $staffMember->is_active =
            !$staffMember->is_active;

        $staffMember->save();


        return back()->with(
            'success',
            $staffMember->is_active
                ? 'Đã kích hoạt tài khoản nhân viên.'
                : 'Đã khóa tài khoản nhân viên.'
        );
    }


    /**
     * Chỉ lấy User có role Staff.
     */
    private function findStaff(
        int $staff
    ): User {

        return User::query()
            ->where(
                'id',
                $staff
            )
            ->whereHas(
                'role',
                function ($roleQuery) {
                    $roleQuery->where(
                        'name',
                        'staff'
                    );
                }
            )
            ->firstOrFail();
    }
}