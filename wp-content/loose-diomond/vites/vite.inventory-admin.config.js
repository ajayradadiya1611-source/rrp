import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

const reactConfig = defineConfig({
  root: path.resolve(__dirname, '../components/inventory-admin/react'),
  plugins: [react()],
  build: {
    outDir: path.resolve(__dirname, '../out/inventory-admin'),
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, '../components/inventory-admin/react/index.html'),
      output: {
        entryFileNames: 'index.js',
        chunkFileNames: 'chunks/[name].js',
        assetFileNames: 'assets/[name][extname]'
      }
    }
  },
})
export default reactConfig
