// Main
const mix = require('laravel-mix')

// Webpack plugins
const ImageminPlugin = require('imagemin-webpack-plugin').default
const CompressionPlugin = require('compression-webpack-plugin')

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

// Javascript
mix.js('resources/js/app.js', 'public/js')

// Stylesheets
mix.postCss('resources/css/app.css', 'public/css')

// Version in prod
if (mix.inProduction()) {
  mix.version()
}

// Assets
mix.copy('resources/assets/images/*.{svg,jpg,png}', 'public/images/')

// Plugins?
const plugins = []

// Minify images
plugins.push(new ImageminPlugin({
  test: /\.(png|svg|jpg)$/,
  disable: !mix.inProduction()
}))

// Brotli and Gzip
const compressionConfig = {
  test: mix.inProduction() ? /\.(js|css|svg)$/ : /^-$/,
  threshold: 1024,
  minRatio: 0.8
}

plugins.push(
  new CompressionPlugin({
    ...compressionConfig,
    filename: '[path]/[name][ext].br[query]',
    algorithm: 'brotliCompress',
    compressionOptions: { level: 11 }
  })
)

plugins.push(
  new CompressionPlugin({
    ...compressionConfig,
    filename: '[path]/[name][ext].gz[query]'
  })
)

// Apply plugins
mix.webpackConfig({ plugins })
