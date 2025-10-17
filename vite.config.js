/*import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',       // permite que otros dispositivos accedan
        strictPort: false,
        hmr: {
            protocol: 'wss',   // usa WebSocket seguro
            host: 'unwisely-unlumpy-cammie.ngrok-free.dev', // tu URL de ngrok
            port: 443,         // ngrok ya usa HTTPS
        },
    },

});
*/


import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
