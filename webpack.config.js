const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    // Copy images from assets to the build directory
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
        // Optional: If versioning is enabled, add the file hash too
        // to: 'images/[path][name].[hash:8].[ext]',
        // Optional: Only copy files matching this pattern
        // pattern: /\.(png|jpg|jpeg)$/
    })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('completed', './assets/completed.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // Add aliases for modules
    .addAliases({
        'chart.js': 'chart.js/dist/Chart.js',
    })
    .enableSingleRuntimeChunk()
    // Remove this line
    // .autoProvidejQuery()
    // Add loaders for CSS and images
    .addLoader({
        test: /\.css$/,
        use: ['style-loader', 'css-loader'],
    })
    .addLoader({
        test: /\.(jpe?g|png|gif)$/i,
        loader: 'file-loader',
        options: {
            name: '[path][name].[ext]',
        },
    })

    // Add this configuration for handling CSS files from node_modules
    .addRule({
        test: /\.css$/,
        include: /node_modules/,
        use: ['style-loader', 'css-loader']
    })

    // Enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // Enable versioning (cache-busting) in production
    .enableVersioning(Encore.isProduction())

    // Enable Sass/SCSS support
    .enableSassLoader()

    // Enable PostCSS support
    .enablePostCssLoader()

    // Add plugin to provide jQuery globally
    .addPlugin(new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    }))

    // Configure Sass loader with the new implementation
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // Remove this line as it's not a valid Encore method
    // .configureSassLoader(options => {
    //     options.implementation = require('sass');
    // })

module.exports = Encore.getWebpackConfig();