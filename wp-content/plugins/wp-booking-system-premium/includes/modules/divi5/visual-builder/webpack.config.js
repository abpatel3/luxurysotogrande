const path = require('path');

module.exports = {
  entry: {
    'single-calendar': './src/single-calendar/index.jsx',
    'overview-calendar': './src/overview-calendar/index.jsx',
    'yearly-calendar': './src/yearly-calendar/index.jsx',
  },

  externals: {
    react: 'React',
  },

  module: {
    rules: [
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'thread-loader',
            options: {
              workers: -1,
            },
          },
          {
            loader: 'babel-loader',
            options: {
              compact: false,
              presets: [
                ['@babel/preset-env', {
                  modules: false,
                  targets: '> 5%',
                }],
                '@babel/preset-react',
              ],
              cacheDirectory: false,
            },
          },
        ],
      },
    ],
  },

  resolve: {
    extensions: ['.js', '.jsx'],
  },

  output: {
    filename: 'wpbs-divi5-[name].js',
    path: path.resolve(__dirname, 'build'),
  },

  optimization: {
    splitChunks: false,
    runtimeChunk: false,
  },
};