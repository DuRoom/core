const config = require('duroom-webpack-config');
const { merge } = require('webpack-merge');

module.exports = merge(config(), {
  output: {
    library: 'duroom.core',
  },
});
