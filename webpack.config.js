/**
 * Webpack config.
 */

var path        = require('path');
var webpack     = require('webpack');
var assetsPath  = path.join('src', 'assets', 'js');
var modulesPath = path.join('src', 'assets', 'js', 'packages');

module.exports = {
  context: path.resolve(assetsPath),
  entry: './main.js',
  output: {
    filename: './webpack.js',
    path: path.resolve(assetsPath)
  },
  resolve: {
    extensions: ['', '.js'],
    root: path.resolve(modulesPath)
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
