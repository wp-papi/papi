/*------------------------------------*\

  WEBPACK CONFIG

\*------------------------------------*/

var webpack = require('webpack');
var path = require('path');
var assets_path = path.join('src', 'assets', 'js');
var modules_path = path.join('src', 'assets', 'js', 'packages');

module.exports = {
  context: path.resolve(assets_path),
  entry: './main.js',
  output: {
    filename: './webpack.js',
    path: path.resolve(assets_path)
  },
  resolve: {
    extensions: ['', '.js'],
    root: path.resolve(modules_path)
  },
  module: {
    loaders: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        loader: 'babel-loader'
      }
    ]
  },
  plugins: [new webpack.optimize.UglifyJsPlugin({ output: {comments: false} })]
};
