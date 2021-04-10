const mix = require('laravel-mix');

const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin')
const CaseSensitivePathsPlugin = require('case-sensitive-paths-webpack-plugin');

var webpackConfig = {

    plugins: [
        new VuetifyLoaderPlugin(),

        new CaseSensitivePathsPlugin()
        // other plugins ...
    ]
    // other webpack config ...
}

mix.webpackConfig(webpackConfig);

mix.browserSync('127.0.0.1:8000');

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

mix.js('resources/js/app.js', 'public/js')
    //Removed .vue because of getting following error:
    //AssertionError [ERR_ASSERTION]: mix.js() is missing required parameter 1: entry
    //This is used for laravel-mix ^6, but not all packages are compatible with webpack 5.
    //.vue()
    .sass('resources/sass/app.scss', 'public/css');
