const path = require('path');

module.exports = {
    entry: './src/js/cz-gallery-plugin.js',
    output: {
        filename: 'main.js',
        path: path.resolve(__dirname, 'js'),
    },
};