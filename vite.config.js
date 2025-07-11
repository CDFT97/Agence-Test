import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/react/views/AgenceView.jsx',
                'resources/js/react/views/InformView.jsx',
                'resources/js/react/views/BarchartView.jsx',
                'resources/js/react/views/PizzaView.jsx',
            ],
            refresh: true,
        }),
        react(),
    ],
});