import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

const reactConfig = defineConfig({
  root: path.resolve(__dirname, '../components/inventory-app/react'),
  plugins: [react()],
  build: {
    outDir: path.resolve(__dirname, '../out/inventory'),
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, '../components/inventory-app/react/index.html'),
      output: {
        entryFileNames: 'index.js',
        chunkFileNames: 'chunks/[name].js',
        assetFileNames: 'assets/[name][extname]'
      }
    }
  },
  optimizeDeps: {
    include: ['react-slick', 'axios'],
  },
  resolve: {
    dedupe: ['axios'],
    alias: {
      'react-slick': path.resolve(__dirname, '../node_modules/react-slick'),
    },
  },
  commonjsOptions: {
    include: [/node_modules/],
  },
})
export default reactConfig
