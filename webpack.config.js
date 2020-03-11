const path = require('path');
const webpack = require('webpack');

module.exports = {
  mode: process.env.NODE === 'production' ? 'production' : 'development',
  context: path.join(__dirname, 'src/assets/js'),
  devtool: 'source-map',
  entry: './main.js',
  externals: {
    jquery: 'jQuery'
  },
  output: {
    filename: './main.min.js',
    path: path.resolve(__dirname, 'dist/js/')
  },
  resolve: {
    extensions: ['.js'],
    modules: [
      path.join(__dirname, 'node_modules'),
      path.join(__dirname, 'src/assets/js')
    ]
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: 'babel-loader'
      }
    ]
  },
  plugins: [new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)]
};
