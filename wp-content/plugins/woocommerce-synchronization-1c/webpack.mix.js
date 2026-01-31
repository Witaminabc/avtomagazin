const mix = require('laravel-mix');

mix.autoload({  // or Mix.autoload() ?
    'jquery': ['$', 'window.jQuery', 'jQuery']
});

mix.setPublicPath('resources/compiled');
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

mix.sass('resources/raw/admin/sass/app.scss', 'admin/css').version();
