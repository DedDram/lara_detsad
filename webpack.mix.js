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
mix.js('resources/js/yandex-map.js', 'public/js/yandex-map.js')
mix.js('resources/js/search-sadik-in-table.js', 'public/js/search-sadik-in-table.js')
mix.js('resources/js/map-sadik.js', 'public/js/map-sadik.js')
mix.js('resources/js/recaptcha.js', 'public/js/recaptcha.js')
mix.js('resources/js/simpleModal.js', 'public/js/simpleModal.js')
mix.js('resources/js/moderation.js', 'public/js/moderation.js')
mix.js('resources/js/comments.js', 'public/js/comments.js')
mix.js('resources/js/recaptchaAdsAndSelect.js', 'public/js/recaptchaAdsAndSelect.js')
