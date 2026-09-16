<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Lưu review cho sản phẩm.
     *
     * Customer chỉ được review sản phẩm đã mua
     * thông qua OrderDetail của chính mình.
     */
    public function store(Request $request, Product $product)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'order_detail_id' => [
                'required',
                'integer',
                'exists:order_details,id',
            ],
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'comment' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $orderDetail = OrderDetail::query()
    ->whereKey($validated['order_detail_id'])
    ->where('product_id', $product->id)
    ->whereHas('order', function ($query) use ($user) {
        $query
            ->where('user_id', $user->id)
            ->where('order_status', 'completed');
    })
    ->first();

        if (!$orderDetail) {
            throw ValidationException::withMessages([
                'review' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua.',
            ]);
        }

        $existingReview = Review::query()
            ->where('user_id', $user->id)
            ->where('order_detail_id', $orderDetail->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            throw ValidationException::withMessages([
                'review' => 'Bạn đã đánh giá sản phẩm này.',
            ]);
        }

        DB::transaction(function () use (
            $user,
            $orderDetail,
            $product,
            $validated
        ) {
            Review::create([
                'user_id' => $user->id,
                'order_detail_id' => $orderDetail->id,
                'review_target' => 'PRODUCT',
                'product_id' => $product->id,
                'custom_design_id' => null,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'edit_count' => 0,
                'is_visible' => true,
            ]);
        });

        return back()->with(
            'success',
            'Đánh giá sản phẩm thành công.'
        );
    }
}