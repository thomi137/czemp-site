const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');

module.exports = {
    mode: 'development',
    entry: './src/js/index.js',
    output: {
        filename: 'main.js',
        path: path.resolve(__dirname, 'dist'),
    },
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/,
                use: ['style-loader', 'css-loader', 'sass-loader'],
                include: path.resolve(__dirname, 'src/scss')
            }

        ],
    },
    plugins: [
        new CopyPlugin({
            patterns: [
                { from: './node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', to: './' },
            ],
        }),
    ],
};