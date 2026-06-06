import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                'resources/css/admin.css',
                'resources/css/login.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/dashboard.js',
                'resources/js/admin/dashboard.js',
                'resources/js/admin/mobil.js',
                'resources/js/admin/pemesanan.js',
                'resources/js/admin/laporan.js',
                'resources/js/admin/chat.js',
                'resources/js/admin/sidebar.js',
                'resources/js/admin/topbar.js',
                'resources/js/admin/user.js',
                'resources/js/echo.js',


                    ],
            refresh: true,
        }),
    ],
});
