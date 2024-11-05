const Encore = require('@symfony/webpack-encore');
const webpack = require('webpack');

Encore
    // Directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // Public path used by the web server to access the output path
    .setPublicPath('/build')

    // Copy images from assets to the build directory
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
    })

    // Entry configuration
    .addEntry('app', './assets/app.js')
    .addEntry('completed', './assets/completed.js')
    // Enable SCSS support
    .enableSassLoader()
    .enableSingleRuntimeChunk()
    // Enable PostCSS support
    .enablePostCssLoader()

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

    // Add plugin to provide jQuery globally
    .addPlugin(new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    }))

    // Configure Babel preset environment
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // Enable DataTables support
    .addEntry('datatables', 'datatables.net-bs5') // Ensure DataTables is included

    // Enable Bootstrap support
    .autoProvidejQuery() // Automatically provide jQuery to modules

module.exports = Encore.getWebpackConfig();