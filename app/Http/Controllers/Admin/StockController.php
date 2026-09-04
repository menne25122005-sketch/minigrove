<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\Product;
use App\Models\StockHistory;
use App\Services\StockService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockController extends Controller
{
    public function __construct(
        private readonly StockService $stockService
    ) {
    }


    /**
     * Danh sách tồn kho Product + Component.
     */
    public function index(Request $request)
    {
        $type = strtoupper(
            trim((string) $request->query('type', 'ALL'))
        );

        if (!in_array(
            $type,
            ['ALL', 'PRODUCT', 'COMPONENT'],
            true
        )) {
            $type = 'ALL';
        }


        $status = trim(
            (string) $request->query('status', '')
        );

        if (!in_array(
            $status,
            [
                '',
                'in_stock',
                'low_stock',
                'out_of_stock',
            ],
            true
        )) {
            $status = '';
        }


        $keyword = trim(
            (string) $request->query('keyword', '')
        );


        /*
         * ----------------------------------------------------------
         * PRODUCT
         * ----------------------------------------------------------
         */
        $products = DB::table('products')
            ->select([
                DB::raw("'PRODUCT' as stock_type"),

                'products.id as item_id',
                'products.name',
                'products.sku',
                'products.stock_quantity',
                'products.low_stock_threshold',

                DB::raw("'Sản phẩm' as item_type_name"),
                DB::raw("'sản phẩm' as unit"),

                'products.is_active',
                'products.updated_at',
            ]);


        /*
         * ----------------------------------------------------------
         * COMPONENT
         * ----------------------------------------------------------
         */
        $components = DB::table('components')
            ->select([
                DB::raw("'COMPONENT' as stock_type"),

                'components.id as item_id',
                'components.name',
                'components.sku',
                'components.stock_quantity',
                'components.low_stock_threshold',

                DB::raw("'Thành phần' as item_type_name"),

                'components.unit',

                'components.is_active',
                'components.updated_at',
            ]);


        /*
         * ----------------------------------------------------------
         * TÌM KIẾM
         * ----------------------------------------------------------
         */
        if ($keyword !== '') {

            $products->where(function ($query) use ($keyword) {

                $query
                    ->where(
                        'products.name',
                        'like',
                        '%' . $keyword . '%'
                    )
                    ->orWhere(
                        'products.sku',
                        'like',
                        '%' . $keyword . '%'
                    );

            });


            $components->where(function ($query) use ($keyword) {

                $query
                    ->where(
                        'components.name',
                        'like',
                        '%' . $keyword . '%'
                    )
                    ->orWhere(
                        'components.sku',
                        'like',
                        '%' . $keyword . '%'
                    );

            });

        }


        /*
         * ----------------------------------------------------------
         * LỌC TRẠNG THÁI KHO
         * ----------------------------------------------------------
         */
        $this->applyStockStatusFilter(
            $products,
            'products',
            $status
        );

        $this->applyStockStatusFilter(
            $components,
            'components',
            $status
        );


        /*
         * ----------------------------------------------------------
         * LỌC LOẠI
         * ----------------------------------------------------------
         */
        if ($type === 'PRODUCT') {

            $stockQuery = $products;

        } elseif ($type === 'COMPONENT') {

            $stockQuery = $components;

        } else {

            $stockQuery = $products->unionAll(
                $components
            );

        }


        /*
         * ----------------------------------------------------------
         * PAGINATION
         * ----------------------------------------------------------
         */
        $stocks = DB::query()
            ->fromSub(
                $stockQuery,
                'stock_items'
            )
            ->orderBy('stock_type')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();


        /*
         * ----------------------------------------------------------
         * THỐNG KÊ NHANH
         * ----------------------------------------------------------
         */
        $productCount = Product::count();

        $componentCount = Component::count();


        $outOfStockCount =
            Product::where(
                'stock_quantity',
                0
            )->count()
            +
            Component::where(
                'stock_quantity',
                0
            )->count();


        $lowStockCount =
            Product::where(
                'stock_quantity',
                '>',
                0
            )
                ->whereColumn(
                    'stock_quantity',
                    '<=',
                    'low_stock_threshold'
                )
                ->count()
            +
            Component::where(
                'stock_quantity',
                '>',
                0
            )
                ->whereColumn(
                    'stock_quantity',
                    '<=',
                    'low_stock_threshold'
                )
                ->count();


        return view(
            'admin.stocks.index',
            compact(
                'stocks',
                'type',
                'status',
                'keyword',
                'productCount',
                'componentCount',
                'outOfStockCount',
                'lowStockCount'
            )
        );
    }


    /**
     * Nhập / xuất / điều chỉnh tồn kho.
     */
    public function change(
        Request $request,
        string $stockType,
        int $itemId
    ) {
        $stockType = strtoupper(
            trim($stockType)
        );

        $request->validate([
            'operation' => [
                'required',
                Rule::in([
                    'import',
                    'export',
                    'adjust',
                ]),
            ],

            'quantity' => [
                'required',
                'integer',
                'min:0',
                'max:1000000',
            ],

            'note' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);


        $operation = $request->string(
            'operation'
        )->toString();

        $quantity = (int) $request->input(
            'quantity'
        );

        $note = trim(
            (string) $request->input('note')
        );


        if (
            in_array(
                $operation,
                ['import', 'export'],
                true
            )
            && $quantity <= 0
        ) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng nhập hoặc xuất kho phải lớn hơn 0.',
            ]);
        }


        if ($operation === 'import') {

            $this->stockService->import(
                stockType: $stockType,
                itemId: $itemId,
                quantity: $quantity,
                updatedBy: auth()->id(),
                note: $note
            );

            $message =
                'Nhập kho thành công.';

        } elseif ($operation === 'export') {

            $this->stockService->export(
                stockType: $stockType,
                itemId: $itemId,
                quantity: $quantity,
                updatedBy: auth()->id(),
                note: $note
            );

            $message =
                'Xuất kho thành công.';

        } else {

            $this->stockService->adjust(
                stockType: $stockType,
                itemId: $itemId,
                newQuantity: $quantity,
                updatedBy: auth()->id(),
                note: $note
            );

            $message =
                'Điều chỉnh tồn kho thành công.';

        }


        return back()->with(
            'success',
            $message
        );
    }


    /**
     * Cập nhật ngưỡng cảnh báo sắp hết hàng.
     */
    public function updateThreshold(
        Request $request,
        string $stockType,
        int $itemId
    ) {
        $validated = $request->validate([
            'low_stock_threshold' => [
                'required',
                'integer',
                'min:0',
                'max:1000000',
            ],
        ]);


        $item = $this->findStockItem(
            $stockType,
            $itemId
        );


        $item->low_stock_threshold =
            (int) $validated[
                'low_stock_threshold'
            ];

        $item->save();


        return back()->with(
            'success',
            'Cập nhật ngưỡng cảnh báo tồn kho thành công.'
        );
    }


    /**
     * Xem lịch sử biến động kho của một item.
     */
    public function history(
        string $stockType,
        int $itemId
    ) {
        $stockType = strtoupper(
            trim($stockType)
        );


        $item = $this->findStockItem(
            $stockType,
            $itemId
        );


        $histories = StockHistory::query()
            ->with([
                'order:id,order_code',
                'updater:id,name',
            ])
            ->where(
                'stock_type',
                $stockType
            );


        if ($stockType === 'PRODUCT') {

            $histories->where(
                'product_id',
                $itemId
            );

        } else {

            $histories->where(
                'component_id',
                $itemId
            );

        }


        $histories = $histories
            ->latest('id')
            ->paginate(15);


        return view(
            'admin.stocks.history',
            compact(
                'item',
                'stockType',
                'histories'
            )
        );
    }


    /**
     * Lọc theo trạng thái tồn kho.
     */
    private function applyStockStatusFilter(
        $query,
        string $table,
        string $status
    ): void {
        if ($status === 'out_of_stock') {

            $query->where(
                $table . '.stock_quantity',
                0
            );

            return;
        }


        if ($status === 'low_stock') {

            $query
                ->where(
                    $table . '.stock_quantity',
                    '>',
                    0
                )
                ->whereColumn(
                    $table . '.stock_quantity',
                    '<=',
                    $table . '.low_stock_threshold'
                );

            return;
        }


        if ($status === 'in_stock') {

            $query->whereColumn(
                $table . '.stock_quantity',
                '>',
                $table . '.low_stock_threshold'
            );

        }
    }


    /**
     * Tìm Product hoặc Component theo stock type.
     */
    private function findStockItem(
        string $stockType,
        int $itemId
    ): Model {
        $stockType = strtoupper(
            trim($stockType)
        );


        if ($stockType === 'PRODUCT') {

            return Product::findOrFail(
                $itemId
            );

        }


        if ($stockType === 'COMPONENT') {

            return Component::findOrFail(
                $itemId
            );

        }


        throw ValidationException::withMessages([
            'stock_type' =>
                'Loại tồn kho không hợp lệ.',
        ]);
    }
}