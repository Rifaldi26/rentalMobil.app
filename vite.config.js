import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                'resources/css/admin.css',
                'resources/css/dashboard.css',
                'resources/css/login.css',
                'resources/css/pemesanan.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/dashboard.js',
                'resources/js/admin/dashboard.js',
                'resources/js/echo.js',


                    ],
            refresh: true,
        }),
    ],
});
