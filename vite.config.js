import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        // Group chart.js and its dependencies
                        if (id.includes('chart.js') || id.includes('@kurkle')) {
                            return 'vendor-charts';
                        }
                        // Group feather-icons
                        if (id.includes('feather-icons')) {
                            return 'vendor-feather';
                        }
                        // Group everything else in node_modules into a general vendor chunk
                        return 'vendor';
                    }
                }
            }
        }
    }
});
