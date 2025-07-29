
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import purge from '@erbelion/vite-plugin-laravel-purgecss';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        purge({
            safelist: [
                // Bootstrap pagination classes
                'pagination',
                'page-item',
                'page-link',
                'active',
                'disabled',
                // Additional pagination-related classes that might be needed
                /^page-/,
                /^pagination-/
            ]
        }),
    ],
});
