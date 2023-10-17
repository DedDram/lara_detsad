const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js('resources/js/yandex-map-vue.js', 'public/js')
    .js('resources/js/search-sadik-in-table.js', 'public/js')
    .vue()
    .webpackConfig({
        optimization: {
            splitChunks: {
                cacheGroups: {
                    vendor: {
                        test: /[\\/]node_modules[\\/]/,
                        name: 'vue',
                        chunks: 'initial',
                    },
                },
            },
        },
    });
