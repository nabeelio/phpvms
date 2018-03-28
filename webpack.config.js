/**
 * Conversion of assets to front-end formats
 */

const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require("extract-text-webpack-plugin");

const extractSass = new ExtractTextPlugin({
    filename: __dirname + "/public/assets/js/[name].css"
});


module.exports = {
    /**
     * Main application files - for the admin, frontend, and the installer
     * They are written out to the /assets/js using the key as the filename
     */
    entry: {
        admin: __dirname + "/resources/js/admin/app.js",
        app: __dirname + "/resources/js/frontend/app.js",
        installer: __dirname + '/resources/js/installer/app.js',
    },
    output: {
        filename: "[name].js",
        path: __dirname + "/public/assets/js/",
    },
    module: {
        rules: [
            /**
             * Admin SASS conversions
             */
            {
                test: /\.scss$/,
                include: [
                    path.resolve(__dirname, 'resources/sass/admin')
                ],
                use: extractSass.extract({
                    use: [{
                        loader: "css-loader"
                    }, {
                        loader: "sass-loader"
                    }]
                })
            }
        ]
    }
};
