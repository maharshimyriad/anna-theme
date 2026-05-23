import { defineConfig } from 'vite';
import liveReload from 'vite-plugin-live-reload';
import path from 'path';

export default defineConfig({
  plugins: [
    liveReload([
      __dirname + '/**/*.php',
    ])
  ],
  root: '',
  base: process.env.NODE_ENV === 'development' ? '/' : '/wp-content/themes/anna-theme/assets/',
  build: {
    outDir: path.resolve(__dirname, './assets'),
    emptyOutDir: false,
    manifest: true,
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, './src/js/main.js'),
        style: path.resolve(__dirname, './src/scss/main.scss')
      },
      output: {
        entryFileNames: `js/[name].js`,
        chunkFileNames: `js/[name].js`,
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith('.css')) {
            return 'css/[name].[ext]';
          }
          return 'images/[name].[ext]';
        }
      }
    }
  },
  server: {
    cors: true,
    strictPort: true,
    port: 3000,
    hmr: {
      host: 'localhost'
    }
  }
});
