var path = require('path')
var config = require('../config')
var utils = require('./utils')
var projectRoot = path.resolve(__dirname, '../')
var webpack = require('webpack')


module.exports = {
  entry: {
    app: './src/main.coffee'
  },
  output: {
    path: config.build.assetsRoot,
    publicPath: process.env.NODE_ENV === 'production' ? config.build.assetsPublicPath : config.dev.assetsPublicPath,
    filename: '[name].js'
  },
  resolve: {
    extensions: ['', '.js', '.vue'],
    fallback: [
        path.join(__dirname, '../src/cm4'),
        path.join(__dirname, '../cm4'),
        path.join(__dirname, '../node_modules')
    ],
    alias: {
      'vue': 'vue/dist/vue.common.js',
      'src': path.resolve(__dirname, '../src')
      // 'assets': path.resolve(__dirname, '../src/assets')
    }
  },
  resolveLoader: {
    fallback: [
      path.join(__dirname, '../src/cm4'),
      path.join(__dirname, '../cm4'),
      path.join(__dirname, '../node_modules')
    ]
  },
  module: {
    loaders: [
      {
        test: /\.vue$/,
        loader: 'vue'
      },
      {
        test: /\.js$/,
        loader: 'babel',
        include: projectRoot,
        exclude: /node_modules/
      },
      {
        test: /\.html$/,
        loader: 'vue-html'
      },
      {
        test: /\.json$/,
        loader: 'json'
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'url',
        query: {
          limit: 10000,
          name: utils.assetsPath('img/[name].[hash:7].[ext]')
        }
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url',
        query: {
          limit: 10000,
          name: utils.assetsPath('fonts/[name].[hash:7].[ext]')
        }
      },
      {
        test: /\.scss$/,
        loader: 'sass',
        exclude: /node_modules/
      },
      {
        test: /\.sass$/,
        loader: 'sass?indentedSyntax',
        exclude: /node_modules/
      },
      {
        test: /\.coffee$/,
        loader: 'babel!coffee',
        exclude: /node_modules/
      },
      {
        test: /pnotify\.js/,
        loader: 'imports?global=>window,this=>window'
      },
      {
        test: require.resolve('tinymce/tinymce'),
        loaders: [
            'imports?this=>window',
            'exports?window.tinymce'
        ]
      },
      {
        test: /tinymce\/(themes|plugins)\//,
        loaders: [
            'imports?this=>window'
        ]
      }
    ]
  },
  vue: {
    loaders: utils.cssLoaders(),
    postcss: [
      require('autoprefixer')({
        browsers: ['last 2 versions']
      })
    ]
  },
  plugins: [
    new webpack.ProvidePlugin({
      $: "jquery/dist/jquery",
      jQuery: "jquery/dist/jquery",
      "window.jQuery": "jquery",
      "window.$": "jquery",
      PNotify: 'pnotify',
      Promise: 'es6-promise'
    })
  ]
}
