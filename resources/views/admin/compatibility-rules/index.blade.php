@extends('layouts.admin')


@section(
    'title',
    'Quản lý quy tắc tương thích - MiniGrove'
)


@section('content')


<div class="card">


    <div class="page-header">


        <h1>
            Quản lý quy tắc tương thích
        </h1>


        <a
            href="{{ route(
                'admin.compatibility-rules.create'
            ) }}"
            class="btn btn-primary"
        >
            + Thêm quy tắc
        </a>


    </div>




    {{-- FILTER --}}

    <form
        action="{{ route(
            'admin.compatibility-rules.index'
        ) }}"
        method="GET"
        class="compatibility-filter"
    >


        <input
            type="text"
            name="keyword"
            value="{{ $keyword }}"
            placeholder="Tên, SKU hoặc lý do..."
        >



        <select name="compatibility">


            <option value="">
                Tất cả kết quả
            </option>


            <option
                value="1"
                @selected(
                    (string) $compatibility === '1'
                )
            >
                Tương thích
            </option>


            <option
                value="0"
                @selected(
                    (string) $compatibility === '0'
                )
            >
                Không tương thích
            </option>


        </select>




        <select name="status">


            <option value="">
                Tất cả trạng thái
            </option>


            <option
                value="1"
                @selected(
                    (string) $status === '1'
                )
            >
                Active
            </option>


            <option
                value="0"
                @selected(
                    (string) $status === '0'
                )
            >
                Inactive
            </option>


        </select>




        <button
            type="submit"
            class="btn btn-primary"
        >
            Lọc
        </button>




        @if(
            $keyword ||
            (
                $compatibility !== null
                &&
                $compatibility !== ''
            )
            ||
            (
                $status !== null
                &&
                $status !== ''
            )
        )


            <a
                href="{{ route(
                    'admin.compatibility-rules.index'
                ) }}"
                class="btn btn-secondary"
            >
                Xóa lọc
            </a>


        @endif



    </form>






    {{-- TABLE --}}


    <div class="table-wrapper">


        <table>


            <thead>


                <tr>

                    <th>
                        ID
                    </th>


                    <th>
                        Sản phẩm nguồn
                    </th>


                    <th>
                        Sản phẩm đích
                    </th>


                    <th>
                        Kết quả
                    </th>


                    <th>
                        Lý do
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


            @forelse($rules as $rule)


                <tr>


                    <td>

                        {{ $rule->id }}

                    </td>





                    {{-- SOURCE PRODUCT --}}


                    <td>


                        <strong>

                            {{ 
                                $rule->sourceProduct?->name 
                                ?? '---'
                            }}

                        </strong>


                        <div class="rule-sku">

                            {{
                                $rule->sourceProduct?->sku
                                ?? ''
                            }}

                        </div>


                    </td>






                    {{-- TARGET PRODUCT --}}


                    <td>


                        <strong>

                            {{
                                $rule->targetProduct?->name
                                ?? '---'
                            }}

                        </strong>


                        <div class="rule-sku">

                            {{
                                $rule->targetProduct?->sku
                                ?? ''
                            }}

                        </div>


                    </td>







                    {{-- COMPATIBILITY --}}


                    <td>


                        @if($rule->is_compatible)


                            <span
                                class="compatibility-compatible"
                            >

                                Tương thích

                            </span>


                        @else


                            <span
                                class="compatibility-incompatible"
                            >

                                Không tương thích

                            </span>


                        @endif


                    </td>






                    {{-- REASON --}}


                    <td class="reason-column">


                        {{
                            $rule->reason ?: '—'
                        }}


                    </td>






                    {{-- STATUS --}}


                    <td>


                        @if($rule->is_active)


                            <span
                                class="status status-active"
                            >

                                Active

                            </span>


                        @else


                            <span
                                class="status status-inactive"
                            >

                                Inactive

                            </span>


                        @endif


                    </td>







                    {{-- ACTION --}}


                    <td>


                        <div class="actions">


                            <a
                                href="{{ route(
                                    'admin.compatibility-rules.edit',
                                    $rule
                                ) }}"
                                class="btn btn-warning"
                            >

                                Sửa

                            </a>





                            <form
                                action="{{ route(
                                    'admin.compatibility-rules.toggle-status',
                                    $rule
                                ) }}"
                                method="POST"
                            >

                                @csrf

                                @method('PATCH')


                                <button
                                    type="submit"
                                    class="btn btn-status"
                                >

                                    {{
                                        $rule->is_active
                                        ? 'Inactive'
                                        : 'Active'
                                    }}

                                </button>


                            </form>







                            <form
                                action="{{ route(
                                    'admin.compatibility-rules.destroy',
                                    $rule
                                ) }}"
                                method="POST"
                                onsubmit="
                                    return confirm(
                                        'Bạn chắc chắn muốn xóa quy tắc này?'
                                    );
                                "
                            >

                                @csrf

                                @method('DELETE')


                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >

                                    Xóa

                                </button>


                            </form>



                        </div>


                    </td>



                </tr>



            @empty


                <tr>


                    <td
                        colspan="7"
                        class="empty"
                    >

                        Không có quy tắc phù hợp.

                    </td>


                </tr>


            @endforelse



            </tbody>


        </table>


    </div>





    <div class="pagination">


        {{ $rules->links() }}


    </div>




</div>



@endsection






@push('styles')


<style>


.compatibility-filter {

    display:grid;

    grid-template-columns:
        minmax(220px,2fr)
        minmax(170px,1fr)
        minmax(150px,1fr)
        auto
        auto;

    gap:9px;

    margin-bottom:20px;

}




.compatibility-filter select {

    border:1px solid #ccd5cf;

    border-radius:7px;

    padding:10px;

    background:white;

}




.rule-sku {

    margin-top:4px;

    color:#777;

    font-size:12px;

}




.compatibility-compatible {

    display:inline-block;

    padding:5px 9px;

    border-radius:20px;

    background:#dcf3e3;

    color:#1d6b38;

}




.compatibility-incompatible {

    display:inline-block;

    padding:5px 9px;

    border-radius:20px;

    background:#fdeaea;

    color:#a33434;

}




.reason-column {

    max-width:280px;

    line-height:1.5;

}




@media(max-width:900px){

    .compatibility-filter{

        grid-template-columns:1fr;

    }

}



</style>


@endpush