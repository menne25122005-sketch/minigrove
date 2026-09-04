document.addEventListener(
    'DOMContentLoaded',
    function () {

        const provinceSelect =
            document.getElementById(
                'checkoutProvince'
            );

        const districtSelect =
            document.getElementById(
                'checkoutDistrict'
            );

        const wardSelect =
            document.getElementById(
                'checkoutWard'
            );


        /*
         * Nếu trang hiện tại không có
         * form thêm Address thì dừng.
         */
        if (
            !provinceSelect ||
            !districtSelect ||
            !wardSelect
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | API
        |--------------------------------------------------------------------------
        */

        const API_BASE =
            'https://provinces.open-api.vn/api/v1';


        /*
        |--------------------------------------------------------------------------
        | OLD VALUES
        |--------------------------------------------------------------------------
        |
        | Dùng khi Laravel validation lỗi
        | và quay lại Checkout.
        |
        */

        const oldProvince =
            provinceSelect.dataset.old || '';

        const oldDistrict =
            districtSelect.dataset.old || '';

        const oldWard =
            wardSelect.dataset.old || '';


        /*
        |--------------------------------------------------------------------------
        | HELPER - RESET SELECT
        |--------------------------------------------------------------------------
        */

        function resetSelect(
            select,
            placeholder
        ) {

            select.innerHTML = '';


            const option =
                document.createElement(
                    'option'
                );


            option.value = '';

            option.textContent =
                placeholder;


            select.appendChild(
                option
            );


            select.disabled = true;
        }


        /*
        |--------------------------------------------------------------------------
        | HELPER - LOADING
        |--------------------------------------------------------------------------
        */

        function showLoading(
            select,
            text
        ) {

            select.innerHTML = '';


            const option =
                document.createElement(
                    'option'
                );


            option.value = '';

            option.textContent = text;


            select.appendChild(
                option
            );


            select.disabled = true;
        }


        /*
        |--------------------------------------------------------------------------
        | HELPER - ERROR
        |--------------------------------------------------------------------------
        */

        function showError(
            select
        ) {

            select.innerHTML = '';


            const option =
                document.createElement(
                    'option'
                );


            option.value = '';

            option.textContent =
                'Không tải được dữ liệu';


            select.appendChild(
                option
            );


            select.disabled = true;
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE OPTION
        |--------------------------------------------------------------------------
        |
        | value:
        | lưu TÊN vào Database.
        |
        | data-code:
        | dùng để gọi API lấy cấp con.
        |
        */

        function createOption(
            item
        ) {

            const option =
                document.createElement(
                    'option'
                );


            option.value =
                item.name;


            option.textContent =
                item.name;


            option.dataset.code =
                item.code;


            return option;
        }


        /*
        |--------------------------------------------------------------------------
        | SELECT OPTION BY NAME
        |--------------------------------------------------------------------------
        */

        function selectByName(
            select,
            name
        ) {

            if (!name) {

                return null;
            }


            const options =
                Array.from(
                    select.options
                );


            const matched =
                options.find(
                    option =>
                        option.value === name
                );


            if (!matched) {

                return null;
            }


            matched.selected = true;


            return matched;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD PROVINCES
        |--------------------------------------------------------------------------
        */

        async function loadProvinces() {

            showLoading(
                provinceSelect,
                'Đang tải Tỉnh / Thành phố...'
            );


            try {

                const response =
                    await fetch(
                        API_BASE + '/p/'
                    );


                if (!response.ok) {

                    throw new Error(
                        'Province API error'
                    );
                }


                const provinces =
                    await response.json();


                provinceSelect.innerHTML = '';


                const defaultOption =
                    document.createElement(
                        'option'
                    );


                defaultOption.value = '';

                defaultOption.textContent =
                    '-- Chọn Tỉnh / Thành phố --';


                provinceSelect.appendChild(
                    defaultOption
                );


                provinces.forEach(
                    province => {

                        provinceSelect
                            .appendChild(
                                createOption(
                                    province
                                )
                            );
                    }
                );


                provinceSelect.disabled =
                    false;


                /*
                 * Restore old Province
                 * nếu Validation quay lại.
                 */
                const oldOption =
                    selectByName(
                        provinceSelect,
                        oldProvince
                    );


                if (oldOption) {

                    await loadDistricts(
                        oldOption.dataset.code,
                        oldDistrict,
                        oldWard
                    );
                }


            } catch (error) {

                console.error(
                    'Không tải được tỉnh:',
                    error
                );


                showError(
                    provinceSelect
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DISTRICTS
        |--------------------------------------------------------------------------
        */

        async function loadDistricts(
            provinceCode,
            restoreDistrict = '',
            restoreWard = ''
        ) {

            resetSelect(
                wardSelect,
                '-- Chọn Phường / Xã --'
            );


            if (!provinceCode) {

                resetSelect(
                    districtSelect,
                    '-- Chọn Quận / Huyện --'
                );

                return;
            }


            showLoading(
                districtSelect,
                'Đang tải Quận / Huyện...'
            );


            try {

                const response =
                    await fetch(
                        API_BASE
                        + '/p/'
                        + provinceCode
                        + '?depth=2'
                    );


                if (!response.ok) {

                    throw new Error(
                        'District API error'
                    );
                }


                const province =
                    await response.json();


                const districts =
                    province.districts || [];


                districtSelect.innerHTML = '';


                const defaultOption =
                    document.createElement(
                        'option'
                    );


                defaultOption.value = '';

                defaultOption.textContent =
                    '-- Chọn Quận / Huyện --';


                districtSelect.appendChild(
                    defaultOption
                );


                districts.forEach(
                    district => {

                        districtSelect
                            .appendChild(
                                createOption(
                                    district
                                )
                            );
                    }
                );


                districtSelect.disabled =
                    false;


                /*
                 * Restore old District.
                 */
                if (restoreDistrict) {

                    const oldOption =
                        selectByName(
                            districtSelect,
                            restoreDistrict
                        );


                    if (oldOption) {

                        await loadWards(
                            oldOption.dataset.code,
                            restoreWard
                        );
                    }
                }


            } catch (error) {

                console.error(
                    'Không tải được quận/huyện:',
                    error
                );


                showError(
                    districtSelect
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD WARDS
        |--------------------------------------------------------------------------
        */

        async function loadWards(
            districtCode,
            restoreWard = ''
        ) {

            if (!districtCode) {

                resetSelect(
                    wardSelect,
                    '-- Chọn Phường / Xã --'
                );

                return;
            }


            showLoading(
                wardSelect,
                'Đang tải Phường / Xã...'
            );


            try {

                const response =
                    await fetch(
                        API_BASE
                        + '/d/'
                        + districtCode
                        + '?depth=2'
                    );


                if (!response.ok) {

                    throw new Error(
                        'Ward API error'
                    );
                }


                const district =
                    await response.json();


                const wards =
                    district.wards || [];


                wardSelect.innerHTML = '';


                const defaultOption =
                    document.createElement(
                        'option'
                    );


                defaultOption.value = '';

                defaultOption.textContent =
                    '-- Chọn Phường / Xã --';


                wardSelect.appendChild(
                    defaultOption
                );


                wards.forEach(
                    ward => {

                        wardSelect
                            .appendChild(
                                createOption(
                                    ward
                                )
                            );
                    }
                );


                wardSelect.disabled =
                    false;


                /*
                 * Restore old Ward.
                 */
                if (restoreWard) {

                    selectByName(
                        wardSelect,
                        restoreWard
                    );
                }


            } catch (error) {

                console.error(
                    'Không tải được phường/xã:',
                    error
                );


                showError(
                    wardSelect
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CHANGE PROVINCE
        |--------------------------------------------------------------------------
        */

        provinceSelect.addEventListener(
            'change',
            function () {

                const selectedOption =
                    provinceSelect
                        .options[
                            provinceSelect
                                .selectedIndex
                        ];


                const provinceCode =
                    selectedOption
                        ?.dataset
                        ?.code;


                loadDistricts(
                    provinceCode
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | CHANGE DISTRICT
        |--------------------------------------------------------------------------
        */

        districtSelect.addEventListener(
            'change',
            function () {

                const selectedOption =
                    districtSelect
                        .options[
                            districtSelect
                                .selectedIndex
                        ];


                const districtCode =
                    selectedOption
                        ?.dataset
                        ?.code;


                loadWards(
                    districtCode
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | INITIAL
        |--------------------------------------------------------------------------
        */

        loadProvinces();

    }
);