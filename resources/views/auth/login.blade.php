<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Đăng nhập - MiniGrove</title>

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
                    Đăng nhập
                </h1>

                <p>
                    Chào mừng bạn quay trở lại MiniGrove.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('login') }}"
            >

                @csrf
                    @if (request('redirect'))

    <input
        type="hidden"
        name="redirect"
        value="{{ request('redirect') }}"
    >

@endif

                <div class="auth-form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        id="email"
                        class="auth-input"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
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
                        Mật khẩu
                    </label>

                    <input
                        id="password"
                        class="auth-input"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Nhập mật khẩu"
                    >

                    @error('password')

                        <div class="auth-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="auth-options">

                    <label class="auth-remember">

                        <input
                            type="checkbox"
                            name="remember"
                        >

                        Ghi nhớ đăng nhập

                    </label>


                    @if (Route::has('password.request'))

                        <a
                            href="{{ route('password.request') }}"
                            class="auth-link"
                        >
                            Quên mật khẩu?
                        </a>

                    @endif

                </div>


                <button
                    type="submit"
                    class="auth-button"
                >
                    ĐĂNG NHẬP
                </button>

            </form>


            <div class="auth-footer">

                Chưa có tài khoản?

                <a
                    href="{{ route('register') }}"
                    class="auth-link"
                >
                    Đăng ký ngay
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