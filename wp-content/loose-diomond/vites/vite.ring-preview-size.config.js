import { defineConfig } from 'vite'
import path from 'path'

const ringPreviewSizeConfig = defineConfig({
  root: path.resolve(__dirname, '../components/ring-preview-size/react'),
  build: {
    outDir: path.resolve(__dirname, '../out/ring-preview-size'),
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, '../components/ring-preview-size/react/index.html'),
      output: {
        entryFileNames: 'index.js',
        chunkFileNames: 'chunks/[name].js',
        assetFileNames: 'assets/[name][extname]'
      }
    }
  },
})
export default ringPreviewSizeConfig
