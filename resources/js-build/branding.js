/**
 * Gumbo configurations
 */

// Get default theme
const defaultTheme = require('tailwindcss/defaultTheme')

const grayColors = {
  50: '#fafafa',
  100: '#f5f5f5',
  200: '#eeeeee',
  300: '#e0e0e0',
  400: '#bdbdbd',
  500: '#9e9e9e',
  600: '#757575',
  700: '#616161',
  800: '#424242',
  900: '#212121'
}

const brandColors = {
  50: '#acf097',
  100: '#86d376',
  200: '#73c465',
  300: '#60b554',
  400: '#4ca643',
  500: '#268922',
  600: '#137a11',
  700: '#006b00',
  800: '#005200',
  900: '#003900'
}

const baseTheme = key => {
  // Get data
  let out = defaultTheme

  // Iterate over dots
  for (let item of key.split('.')) {
    // Get item
    item = String(item)

    // Fail if missing
    if (!out.hasOwnProperty(item)) {
      console.warn('Failed for %o at %o in %o', key, item, out)
      return null
    }

    // Descend
    out = out[item]
  }

  // Return result
  return out
}

const colors = {
  light: defaultTheme.colors.white,
  dark: grayColors['900'],

  // Gray
  gray: grayColors,
  // Red
  red: baseTheme('colors.red'),
  // Orange
  orange: baseTheme('colors.orange'),
  // Green
  green: baseTheme('colors.green'),
  // Blue
  blue: baseTheme('colors.blue'),
  // Brand
  brand: brandColors
}

module.exports = {
  colors: colors,
  plugins: [
    require('@tailwindcss/custom-forms')
  ]
}
