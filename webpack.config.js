var path        = require('path');
var webpack     = require('webpack');

module.exports = {
  context: path.join(__dirname, 'src/assets/js'),
  devtool: 'source-map',
  entry: './main.js',
  externals: {
    'jquery': 'jQuery'
  },
  output: {
    filename: './main.min.js',
    path: 'dist/js/'
  },
  resolve: {
    extensions: ['', '.js'],
    root: [
      path.join(__dirname, 'node_modules'),
      path.join(__dirname, 'src/assets/js')
    ]
  },
  module: {
    loaders: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        loader: 'babel',
        query: {
          presets: ['es2015']
        }
      }
    ]
  },
  plugins: [
    new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
    new webpack.optimize.UglifyJsPlugin({minimize: true})
  ]
};
