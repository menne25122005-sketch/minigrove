<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Thêm địa chỉ giao hàng.
     */
    public function store(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate(
            [
                'receiver_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                ],

                'receiver_phone' => [
                    'required',
                    'regex:/^0\d{9}$/',
                ],

                'province' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'district' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'ward' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'address_detail' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'label' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'is_default' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'receiver_name.required' =>
                    'Vui lòng nhập tên người nhận.',

                'receiver_phone.required' =>
                    'Vui lòng nhập số điện thoại.',

                'receiver_phone.regex' =>
                    'Số điện thoại phải gồm 10 số và bắt đầu bằng 0.',

                'province.required' =>
                    'Vui lòng nhập Tỉnh/Thành phố.',

                'district.required' =>
                    'Vui lòng nhập Quận/Huyện.',

                'ward.required' =>
                    'Vui lòng nhập Phường/Xã.',

                'address_detail.required' =>
                    'Vui lòng nhập địa chỉ chi tiết.',
            ]
        );


        $user =
            $request->user();


        /*
         * Địa chỉ đầu tiên tự động
         * trở thành mặc định.
         */
        $makeDefault =
            $request->boolean('is_default')
            ||
            !$user->addresses()->exists();


        $address = DB::transaction(
            function () use (
                $user,
                $validated,
                $makeDefault
            ) {

                if ($makeDefault) {

                    $user->addresses()
                        ->update([
                            'is_default' =>
                                false,
                        ]);
                }


                return $user->addresses()
                    ->create([
                        'receiver_name' =>
                            $validated[
                                'receiver_name'
                            ],

                        'receiver_phone' =>
                            $validated[
                                'receiver_phone'
                            ],

                        'province' =>
                            $validated[
                                'province'
                            ],

                        'district' =>
                            $validated[
                                'district'
                            ],

                        'ward' =>
                            $validated[
                                'ward'
                            ],

                        'address_detail' =>
                            $validated[
                                'address_detail'
                            ],

                        'label' =>
                            $validated[
                                'label'
                            ]
                            ?? null,

                        'is_default' =>
                            $makeDefault,
                    ]);
            }
        );


        /*
         * Tự chọn địa chỉ vừa thêm
         * cho Checkout hiện tại.
         */
        session([
            'checkout.address_id'
                => $address->id,
        ]);


        return redirect()
            ->route('checkout.index')
            ->with(
                'success',
                'Đã thêm địa chỉ giao hàng.'
            );
    }


    /**
     * Chọn một địa chỉ đã lưu.
     */
    public function select(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate([
            'address_id' => [
                'required',
                'integer',
            ],
        ]);


        /*
         * Quan trọng:
         * chỉ lấy Address thuộc User hiện tại.
         */
        $address =
            $request->user()
                ->addresses()
                ->findOrFail(
                    $validated[
                        'address_id'
                    ]
                );


        session([
            'checkout.address_id'
                => $address->id,
        ]);


        return redirect()
            ->route('checkout.index')
            ->with(
                'success',
                'Đã chọn địa chỉ giao hàng.'
            );
    }
}