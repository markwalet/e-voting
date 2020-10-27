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
  gray: {
    'primary-1': grayColors['500'],
    'primary-2': grayColors['600'],
    'primary-3': grayColors['700'],
    'secondary-1': grayColors['200'],
    'secondary-2': grayColors['300'],
    'secondary-3': grayColors['400']
  },
  // Red
  red: {
    'primary-1': baseTheme('colors.red.500'),
    'primary-2': baseTheme('colors.red.600'),
    'primary-3': baseTheme('colors.red.700'),
    'secondary-1': baseTheme('colors.red.100'),
    'secondary-2': baseTheme('colors.red.200'),
    'secondary-3': baseTheme('colors.red.300')
  },
  // Orange
  orange: {
    'primary-1': baseTheme('colors.orange.500'),
    'primary-2': baseTheme('colors.orange.600'),
    'primary-3': baseTheme('colors.orange.700'),
    'secondary-1': baseTheme('colors.orange.100'),
    'secondary-2': baseTheme('colors.orange.200'),
    'secondary-3': baseTheme('colors.orange.300')
  },
  // Green
  green: {
    'primary-1': baseTheme('colors.green.500'),
    'primary-2': baseTheme('colors.green.600'),
    'primary-3': baseTheme('colors.green.700'),
    'secondary-1': baseTheme('colors.green.100'),
    'secondary-2': baseTheme('colors.green.200'),
    'secondary-3': baseTheme('colors.green.300')
  },
  // Blue
  blue: {
    'primary-1': baseTheme('colors.blue.500'),
    'primary-2': baseTheme('colors.blue.600'),
    'primary-3': baseTheme('colors.blue.700'),
    'secondary-1': baseTheme('colors.blue.100'),
    'secondary-2': baseTheme('colors.blue.200'),
    'secondary-3': baseTheme('colors.blue.300')
  },
  // Brand
  brand: {
    'primary-1': brandColors['500'],
    'primary-2': brandColors['600'],
    'primary-3': brandColors['700'],
    'secondary-1': brandColors['100'],
    'secondary-2': brandColors['200'],
    'secondary-3': brandColors['300']
  }
}

module.exports = {
  colors: colors,
  plugins: [
    require('@tailwindcss/custom-forms')
  ]
}
