<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Component;
use App\Models\ComponentCompatibilityRule;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $productCount = Product::count();

        $categoryCount = Category::count();

        $componentCount = Component::count();

        $compatibilityRuleCount =
            ComponentCompatibilityRule::count();

        $customerCount = User::whereHas(
            'role',
            function ($query) {
                $query->where(
                    'name',
                    'customer'
                );
            }
        )->count();

        $orderCount = Order::count();

        return view(
            'admin.dashboard',
            compact(
                'productCount',
                'categoryCount',
                'componentCount',
                'compatibilityRuleCount',
                'customerCount',
                'orderCount'
            )
        );
    }
}