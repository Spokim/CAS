import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/js/codex-editor.js",
                "resources/js/jquery.js",
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '$': 'jQuery',
        }
    },
    build: {
        minify: false,
    },
    server: {
        port: 80,
        strictPort: true,
        hmr: {
            host: 'localhost',
            port: 80,
        }
    }
});
