import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/backend/admin.css',
                'resources/css/backend/admin-custom.css',
                'resources/js/backend/admin.js',
                'resources/css/backend/media-gallery.css',
                'resources/js/backend/media-gallery.js',
                'resources/js/backend/admin-media.js'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
