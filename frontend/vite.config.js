import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import EnvironmentPlugin from 'vite-plugin-environment';
import path from 'path';

// https://vite.dev/config/

export default defineConfig(({ mode }) => {
    const isDevelopment = mode === "development";
    return {
        server: {
            port: 3000,
        },
        build: {
            outDir: "./../public/app",
        },
        base: isDevelopment ? "/" : "/app/",
        plugins: [
            vue(),
            EnvironmentPlugin({
                NODE_ENV: 'development',
            }),
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './src'),
            },
        },
        
    };
});
