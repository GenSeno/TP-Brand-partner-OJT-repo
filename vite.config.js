import vue from '@vitejs/plugin-vue';
import { BootstrapVueNextResolver } from 'bootstrap-vue-next/resolvers';
import laravel from 'laravel-vite-plugin';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/js/app.js',
        'resources/js/bootstrap.js',
        'resources/js/brand-partner.js',
      ],
      refresh: true,
    }),

    tailwindcss(),

    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),

    Components({
      resolvers: [BootstrapVueNextResolver()],
    }),
  ],
  optimizeDeps: {
    exclude: ['chunk-2N4YFDUS.js', 'chunk-WISF2N3T.js'],
  },
});
