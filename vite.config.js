import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // MUDANÇA AQUI: Alterado de '0.0.0.0' para 'localhost'
        host: 'localhost', 
        port: 5173,
        strictPort: true,
        ...(process.env.DDEV_PRIMARY_URL_WITHOUT_PORT
            ? {
                  origin: `${process.env.DDEV_PRIMARY_URL_WITHOUT_PORT}:5173`,
                  cors: {
                      origin: /https?:\/\/([A-Za-z0-9\-\.]+)?(\.ddev\.site)(?::\d+)?$/,
                  },
              }
            : {}),
    },
});