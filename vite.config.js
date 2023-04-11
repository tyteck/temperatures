import { defineConfig } from 'vite';
import { viteStaticCopy } from 'vite-plugin-static-copy'
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        viteStaticCopy({
            targets: [
                {
                    src: 'node_modules/chart.js/dist/chart.js',
                    dest: 'js'
                }
            ]
        }),
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
});



