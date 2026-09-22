<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Đặt lại mật khẩu - MiniGrove</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/auth.css') }}"
    >

</head>

<body>

<div class="auth-page">

    <section class="auth-brand">

        <div class="auth-brand-content">

            <div class="auth-logo">
                🌿 MiniGrove
            </div>

            <div class="auth-slogan">
                Nhỏ một khu rừng,<br>
                lớn một thế giới.
            </div>

            <div class="auth-description">
                Khám phá những hệ sinh thái mini,
                tự thiết kế Terrarium và đồng hành
                cùng khu vườn của riêng bạn.
            </div>

            <div class="auth-features">

                <span class="auth-feature">
                    Terrarium
                </span>

                <span class="auth-feature">
                    MiniGrove Studio
                </span>

                <span class="auth-feature">
                    My Grove
                </span>

                <span class="auth-feature">
                    GroveCare
                </span>

            </div>

        </div>

    </section>


    <main class="auth-main">

        <div class="auth-box">

            <div class="auth-heading">

                <h1>
                    Đặt lại mật khẩu
                </h1>

                <p>
                    Tạo mật khẩu mới cho tài khoản MiniGrove của bạn.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('password.store') }}"
            >

                @csrf


                <input
                    type="hidden"
                    name="token"
                    value="{{ $request->route('token') }}"
                >


                <div class="auth-form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        id="email"
                        class="auth-input"
                        type="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="example@gmail.com"
                    >

                    @error('email')

                        <div class="auth-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="auth-form-group">

                    <label for="password">
                        Mật khẩu mới
                    </label>

                    <input
                        id="password"
                        class="auth-input"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Nhập mật khẩu mới"
                    >

                    @error('password')

                        <div class="auth-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="auth-form-group">

                    <label for="password_confirmation">
                        Xác nhận mật khẩu
                    </label>

                    <input
                        id="password_confirmation"
                        class="auth-input"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Nhập lại mật khẩu mới"
                    >

                    @error('password_confirmation')

                        <div class="auth-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <button
                    type="submit"
                    class="auth-button"
                >
                    ĐẶT LẠI MẬT KHẨU
                </button>

            </form>


            <div class="auth-footer">

                Nhớ mật khẩu rồi?

                <a
                    href="{{ route('login') }}"
                    class="auth-link"
                >
                    Đăng nhập
                </a>

            </div>


            <div class="auth-back">

                <a
                    href="{{ route('home') }}"
                    class="auth-link"
                >
                    ← Trở về trang chủ
                </a>

            </div>

        </div>

    </main>

</div>

</body>

</html>