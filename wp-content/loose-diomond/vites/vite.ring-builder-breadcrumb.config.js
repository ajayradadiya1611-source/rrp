import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

const reactConfig = defineConfig({
  root: path.resolve(__dirname, '../components/ring-builder-breadcrumb/react'),
  plugins: [react()],
  build: {
    outDir: path.resolve(__dirname, '../out/ring-builder-breadcrumb'),
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, '../components/ring-builder-breadcrumb/react/index.html'),
      output: {
        entryFileNames: 'index.js',
        chunkFileNames: 'chunks/[name].js',
        assetFileNames: 'assets/[name][extname]'
      }
    }
  },
})
export default reactConfig
