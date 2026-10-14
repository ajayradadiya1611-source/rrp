import { defineConfig } from 'vite'
import path from 'path'

// Build theme SCSS into a standalone CSS file at assets/css/styles.css
export default defineConfig({
  root: path.resolve(__dirname),
  build: {
    outDir: path.resolve(__dirname, '../out/theme'),
    emptyOutDir: false,
    rollupOptions: {
      input: path.resolve(__dirname, '../sass/styles.js'),
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/chunks/[name].js',
        assetFileNames: 'css/[name][extname]'
      }
    }
  },
})
