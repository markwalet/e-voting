/* eslint-disable quote-props */
// Plugins
const autoprefixer = require('autoprefixer')
const cssnano = require('cssnano')
const postcssCalc = require('postcss-calc')
const postcssImport = require('postcss-import')
const tailwindcss = require('tailwindcss')

module.exports = ({ file, options, env }) => {
  const isProduction = env === 'production'

  const plugins = [
    postcssImport(),
    tailwindcss(),
    postcssCalc({}),
    autoprefixer()
    // cssnano
  ]

  if (isProduction) {
    // Add cssnano as last
    plugins.push(cssnano(options.cssnano))
  }

  return {
    parser: false,
    plugins: plugins
  }
}
