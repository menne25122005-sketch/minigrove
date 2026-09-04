<x-guest-layout>

    <form
        method="POST"
        action="{{ route('register') }}"
    >

        @csrf


        {{-- Họ và tên --}}
        <div>

            <x-input-label
                for="name"
                value="Họ và tên"
            />

            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="Ví dụ: Nguyễn Văn An"
            />

            <x-input-error
                :messages="$errors->get('name')"
                class="mt-2"
            />

        </div>


        {{-- Email --}}
        <div class="mt-4">

            <x-input-label
                for="email"
                value="Email"
            />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="email"
                placeholder="example@gmail.com"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />

        </div>


        {{-- Số điện thoại --}}
        <div class="mt-4">

            <x-input-label
                for="phone"
                value="Số điện thoại"
            />

            <x-text-input
                id="phone"
                class="block mt-1 w-full"
                type="tel"
                name="phone"
                :value="old('phone')"
                required
                autocomplete="tel"
                maxlength="10"
                placeholder="0901234567"
            />

            <x-input-error
                :messages="$errors->get('phone')"
                class="mt-2"
            />

        </div>


        {{-- Mật khẩu --}}
        <div class="mt-4">

            <x-input-label
                for="password"
                value="Mật khẩu"
            />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <p class="mt-1 text-sm text-gray-500">
                Từ 8–64 ký tự, gồm chữ hoa,
                chữ thường, số và ký tự đặc biệt.
            </p>

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />

        </div>


        {{-- Xác nhận mật khẩu --}}
        <div class="mt-4">

            <x-input-label
                for="password_confirmation"
                value="Xác nhận mật khẩu"
            />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get(
                    'password_confirmation'
                )"
                class="mt-2"
            />

        </div>


        <div
            class="
                flex
                items-center
                justify-end
                mt-4
            "
        >

            <a
                class="
                    underline
                    text-sm
                    text-gray-600
                    hover:text-gray-900
                    rounded-md
                "
                href="{{ route('login') }}"
            >
                Bạn đã có tài khoản?
            </a>


            <x-primary-button class="ms-4">
                Đăng ký
            </x-primary-button>

        </div>

    </form>

</x-guest-layout>