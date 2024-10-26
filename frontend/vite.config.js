import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

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
        plugins: [vue()],
    };
});
