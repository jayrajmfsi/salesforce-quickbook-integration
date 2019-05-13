var Encore = require('@symfony/webpack-encore');
var webpack = require('webpack');
var path = require('path');

Encore
// directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // what's the public path to this directory (relative to your project's document root dir)
    .setPublicPath('/build')

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()
    .disableSingleRuntimeChunk()
    // will output as web/build/app.js
    .addEntry('app', './src/AppBundle/Resources/public/js/app.js')

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

;

// export the final configuration
let config = Encore.getWebpackConfig();
config.resolve.alias = {
    'jquery': path.join(__dirname, 'node_modules/jquery/dist/jquery')
};
module.exports = config;
