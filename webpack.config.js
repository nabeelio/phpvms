'use strict';

const webpack = require('webpack');

module.exports = {
    entry: {
        admin: __dirname + "/resources/js/admin/app.js",
        app: __dirname + "/resources/js/frontend/app.js",
    },
    output: {
        filename: "[name].js",
        path: __dirname + "/public/js/",
    }
};
