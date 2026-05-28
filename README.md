<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
```
rentalMobil.app
├─ .editorconfig
├─ app
│  ├─ Http
│  │  ├─ Controllers
│  │  │  ├─ Auth
│  │  │  │  ├─ AuthenticatedSessionController.php
│  │  │  │  ├─ ConfirmablePasswordController.php
│  │  │  │  ├─ EmailVerificationNotificationController.php
│  │  │  │  ├─ EmailVerificationPromptController.php
│  │  │  │  ├─ NewPasswordController.php
│  │  │  │  ├─ PasswordController.php
│  │  │  │  ├─ PasswordResetLinkController.php
│  │  │  │  ├─ RegisteredUserController.php
│  │  │  │  └─ VerifyEmailController.php
│  │  │  ├─ Controller.php
│  │  │  ├─ MobilController.php
│  │  │  ├─ PemesananController.php
│  │  │  └─ ProfileController.php
│  │  ├─ Middleware
│  │  │  └─ IsAdmin.php
│  │  └─ Requests
│  │     ├─ Auth
│  │     │  └─ LoginRequest.php
│  │     └─ ProfileUpdateRequest.php
│  ├─ Models
│  │  ├─ Mobil.php
│  │  ├─ Pemesanan.php
│  │  └─ User.php
│  ├─ Providers
│  │  └─ AppServiceProvider.php
│  └─ View
│     └─ Components
│        ├─ AppLayout.php
│        └─ GuestLayout.php
├─ artisan
├─ bootstrap
│  ├─ app.php
│  ├─ cache
│  │  ├─ packages.php
│  │  └─ services.php
│  └─ providers.php
├─ composer.json
├─ composer.lock
├─ config
│  ├─ app.php
│  ├─ auth.php
│  ├─ cache.php
│  ├─ database.php
│  ├─ filesystems.php
│  ├─ logging.php
│  ├─ mail.php
│  ├─ queue.php
│  ├─ services.php
│  └─ session.php
├─ daftarfitur.xlsx
├─ database
│  ├─ factories
│  │  └─ UserFactory.php
│  ├─ migrations
│  │  ├─ 0001_01_01_000000_create_users_table.php
│  │  ├─ 0001_01_01_000001_create_cache_table.php
│  │  ├─ 0001_01_01_000002_create_jobs_table.php
│  │  ├─ 2026_05_21_134623_add_role_to_users_table.php
│  │  ├─ 2026_05_23_055134_create_mobils_table.php
│  │  ├─ 2026_05_23_055148_create_pemesanans_table.php
│  │  ├─ 2026_05_23_055655_add_no_hp_to_users_table.php
│  │  └─ 2026_05_23_110200_simplify_role_enum_in_users_table.php
│  └─ seeders
│     └─ DatabaseSeeder.php
├─ note.txt
├─ package-lock.json
├─ package.json
├─ phpunit.xml
├─ postcss.config.js
├─ public
│  ├─ .htaccess
│  ├─ favicon.ico
│  ├─ index.php
│  └─ robots.txt
├─ README.md
├─ rentalMobil.app-main.zip
├─ resources
│  ├─ css
│  │  ├─ admin.css
│  │  ├─ app.css
│  │  ├─ dashboard.css
│  │  ├─ login.css
│  │  └─ pemesanan.css
│  ├─ js
│  │  ├─ admin
│  │  │  └─ dashboard.js
│  │  ├─ app.js
│  │  ├─ bootstrap.js
│  │  └─ dashboard.js
│  └─ views
│     ├─ admin
│     │  ├─ chat.blade.php
│     │  ├─ dashboard.blade.php
│     │  ├─ mobil
│     │  │  ├─ create.blade.php
│     │  │  ├─ edit.blade.php
│     │  │  └─ index.blade.php
│     │  ├─ partials
│     │  │  └─ bottom-nav.blade.php
│     │  ├─ pemesanan
│     │  │  ├─ create.blade.php
│     │  │  └─ index.blade.php
│     │  └─ profil.blade.php
│     ├─ auth
│     │  ├─ confirm-password.blade.php
│     │  ├─ forgot-password.blade.php
│     │  ├─ login.blade.php
│     │  ├─ register.blade.php
│     │  ├─ reset-password.blade.php
│     │  └─ verify-email.blade.php
│     ├─ components
│     │  ├─ application-logo.blade.php
│     │  ├─ auth-session-status.blade.php
│     │  ├─ danger-button.blade.php
│     │  ├─ dropdown-link.blade.php
│     │  ├─ dropdown.blade.php
│     │  ├─ input-error.blade.php
│     │  ├─ input-label.blade.php
│     │  ├─ modal.blade.php
│     │  ├─ nav-link.blade.php
│     │  ├─ primary-button.blade.php
│     │  ├─ responsive-nav-link.blade.php
│     │  ├─ secondary-button.blade.php
│     │  └─ text-input.blade.php
│     ├─ layouts
│     │  ├─ app.blade.php
│     │  ├─ guest.blade.php
│     │  └─ navigation.blade.php
│     ├─ profile
│     │  ├─ edit.blade.php
│     │  └─ partials
│     │     ├─ delete-user-form.blade.php
│     │     ├─ update-password-form.blade.php
│     │     └─ update-profile-information-form.blade.php
│     ├─ users
│     │  ├─ chat.blade.php
│     │  ├─ dashboard.blade.php
│     │  ├─ favorit.blade.php
│     │  ├─ partials
│     │  ├─ pemesanan
│     │  └─ profil.blade.php
│     └─ welcome.blade.php
├─ routes
│  ├─ auth.php
│  ├─ console.php
│  └─ web.php
├─ storage
│  ├─ app
│  │  ├─ private
│  │  └─ public
│  │     └─ mobil
│  │        ├─ bmZd0HKdahXdokeWNvxMGkJrqArX3FEpyKflPMJj.webp
│  │        ├─ nZ2hDKBYIzkN2nBA0RcJbxyiAQI2pcXYHOrDMrsx.png
│  │        └─ Toyota Innova Reborn.png
│  ├─ framework
│  │  ├─ cache
│  │  │  └─ data
│  │  ├─ sessions
│  │  ├─ testing
│  │  └─ views
│  │     ├─ 09bef3cd86f0a7a63e6b41f4fa94cccb.php
│  │     ├─ 09da6966d304752d2bce5c4b2886d4d3.php
│  │     ├─ 0c5b4082ee1726c6326cfc19eff8eb38.php
│  │     ├─ 0d6289a11ea8a152a2f9fd0734e85ca3.php
│  │     ├─ 0ec3a0d7b11ef6efc5843cdabdbc46e9.php
│  │     ├─ 1086d664425be0b554a97b2eabe19440.php
│  │     ├─ 117fb81541f072bc4196631937a69d09.php
│  │     ├─ 13ae8292377ed3c1d6fff31c4cde097a.php
│  │     ├─ 1d5dc9846d3d060ac85da66fa6e09a93.php
│  │     ├─ 20a13de052e13654bfe37cf9e469b2bd.php
│  │     ├─ 22c058dfd6a152bcd4cde294d02ad3cd.php
│  │     ├─ 235b9e91d1639f64b5aa46b118c0b7d0.php
│  │     ├─ 26e282d4a390c923eba1204df9690d95.php
│  │     ├─ 27139df409de78bc1a74fc7d8467ebec.php
│  │     ├─ 277adb96d070c25faced9d61b9471342.php
│  │     ├─ 28f29872cb18c1f8a7b997a142cc98fe.php
│  │     ├─ 2d2fc717ab535f7388a904dac76d6c20.php
│  │     ├─ 2e0792751e1e4a71c215f31369bc3e9e.php
│  │     ├─ 2ed94daeeec0df89843464e9a69a0094.php
│  │     ├─ 2edefd5100480f41611778eb50614490.php
│  │     ├─ 3192c9ada6bbf0078ff16caf52db9ee4.php
│  │     ├─ 31ed23437993fb4964023ae638b4e057.php
│  │     ├─ 3561b108a6bcc8f4deabb6dfd2aa2fa7.php
│  │     ├─ 36c7bffea7e86feb2e04e59fc0037067.php
│  │     ├─ 40996f4864c63421a94be0416961bd56.php
│  │     ├─ 41a1928e0f33ece16e2354754ffef028.php
│  │     ├─ 44952e7b39fec67c69110a6c35130774.php
│  │     ├─ 4b60f6c7b994b44826c3b3fca9dcc71f.php
│  │     ├─ 4e8ee27bc5c5facb2c88872cf87d91e2.php
│  │     ├─ 4f4c9d898a04a980110a89ea23508a25.php
│  │     ├─ 4ffbd6f726ecde70d8601564734d83e9.php
│  │     ├─ 57d48b0211254adeb8c8bae906cf3753.php
│  │     ├─ 58449e776c075a4f3693925fac5875bd.php
│  │     ├─ 5b70dddd5f4a799a03742b99c12b8951.php
│  │     ├─ 5e898e5e6e413d58872ef73301e34511.php
│  │     ├─ 5f8b84b402ca9fc68c0489a90a1989e2.php
│  │     ├─ 60b351ce8e95a585c6f03688f06db36e.php
│  │     ├─ 65f1cf9fb65332b949097d10c5e2d038.php
│  │     ├─ 69f364f9b930eb69871010bfe686b21d.php
│  │     ├─ 72b4544a0ccb78cea0d34824a403e027.php
│  │     ├─ 8096c870ac38214d8f16e620c4eac049.php
│  │     ├─ 80e35e24a20153f6d011956cbebb8222.php
│  │     ├─ 86e627ffa43185e93301dbb68ac4c76b.php
│  │     ├─ 8816406af9216821e5bd37b71b1b1715.php
│  │     ├─ 99aff930cd01a62ad536432fbc201085.php
│  │     ├─ 9c88123f9be3f1821560074efe00a582.php
│  │     ├─ a2e1c2b841e4303ccfa2fa135973d0e8.php
│  │     ├─ a6ec00592bf6d19beca565abe6de1b8a.php
│  │     ├─ a8fdb8e1b226a7b533da690caf65820c.php
│  │     ├─ aa3e85ef451ee36cc00c983227656218.php
│  │     ├─ aa91e1127ee20b288f43d7e1df08460d.php
│  │     ├─ aae5ca19891b5720081bad8144b6cc0f.php
│  │     ├─ b339dc78848a8668f4c61d363ea1e635.php
│  │     ├─ b4eec9bba4984f5b24d403bbc854693e.php
│  │     ├─ b7c6f486e038181c9c6f49171a758b90.php
│  │     ├─ d01d9c4f69b46f3fbf1f3b95bc08a7b1.php
│  │     ├─ d3658153de35c59b43e5cff4d25d6876.php
│  │     ├─ d44579f0f6adf9961da07ef56914b828.php
│  │     ├─ d8871efbfe958dec761510bf6bb24921.php
│  │     ├─ e43e880ea1b5ec1b46d13124575de45c.php
│  │     ├─ e4a4d1c265b91209f4251b1d6d3a936e.php
│  │     ├─ e7c38b0045d2324badc44c4cb25f03fa.php
│  │     ├─ ec8bf2cd6d852c7f2f33f43263fa423e.php
│  │     ├─ f19a6f6f4e050d6274b9b5d52cbe1208.php
│  │     ├─ f3fcedeee90354833c84ddd98126c9eb.php
│  │     ├─ f60cff43899dcf9e76bb6b3e43816a58.php
│  │     ├─ f68f271b17a6a4c3721ffa7aa689576e.php
│  │     ├─ fc0a2a976e164c806e4d9eaa9cfe8881.php
│  │     ├─ fcec68c9e8b2a610ead56938d760e626.php
│  │     ├─ fd8a6812542c9c23d619f1bbd47f3ffe.php
│  │     └─ fee6563fe51d83a06b83d98c981a5d4e.php
│  └─ logs
├─ tailwind.config.js
├─ tests
│  ├─ Feature
│  │  ├─ Auth
│  │  │  ├─ AuthenticationTest.php
│  │  │  ├─ EmailVerificationTest.php
│  │  │  ├─ PasswordConfirmationTest.php
│  │  │  ├─ PasswordResetTest.php
│  │  │  ├─ PasswordUpdateTest.php
│  │  │  └─ RegistrationTest.php
│  │  ├─ ExampleTest.php
│  │  └─ ProfileTest.php
│  ├─ TestCase.php
│  └─ Unit
│     └─ ExampleTest.php
└─ vite.config.js

```