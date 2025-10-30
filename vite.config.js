import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [laravel({
    input: [
      'resources/css/app.css',
      'resources/js/app.js',
      'resources/js/camera.js', // adicione esta linha se faltar
      'resources/js/profile-photo.js'
    ],
    refresh: true,
  })]
});