import path from "path"
import laravel from "laravel-vite-plugin"
import vue from "@vitejs/plugin-vue"
import { defineConfig } from "vite"

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    laravel({
      input: ["src/main.ts"],
      refresh: true,
    }),
    vue(),
  ],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  server: {
    proxy: {
      "/api": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
      },
    },
  },
});
