import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

const reactConfig = defineConfig({
  root: path.resolve(__dirname, '../components/zero-price-products/react'),
  plugins: [react()],
  build: {
    outDir: path.resolve(__dirname, '../out/zero-price-products'),
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, '../components/zero-price-products/react/index.html'),
      output: {
        entryFileNames: 'index.js',
        chunkFileNames: 'chunks/[name].js',
        assetFileNames: 'assets/[name][extname]'
      }
    }
  },
  optimizeDeps: {
    include: ['axios'],
  },
  resolve: {
    dedupe: ['axios'],
  },
})
export default reactConfig
