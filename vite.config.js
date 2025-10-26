import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    base: '/evaluacion', // 🔧 ajusta según tu ruta base
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true, // 🔁 auto-reload al cambiar Blade o Tailwind
        }),
    ],

    // 🧩 Configuración adicional recomendada
    css: {
        devSourcemap: true, // facilita depurar estilos
    },

    // 🖥️ Soporte óptimo para Windows (Laragon)
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },

    server: {
        host: '127.0.0.1',
        port: 5173,
        open: false,
        cors: true,
        hmr: {
            overlay: true, // muestra errores de Tailwind claramente
        },
    },


});
