const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.scripts([
    'public/assets/js/vendors.js',
    'public/assets/js/aiz-core.js',
    'public/assets/js/datatables.bundle.js',
], 'public/js/plugins.js').styles([
    'public/assets/css/vendors.css',
    'public/assets/css/aiz-core.css',
    'public/assets/css/custom-style.css',
    'public/assets/css/datatables.bundle.css',
], 'public/css/plugins.css');

