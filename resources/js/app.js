import axios from './axios'
import LoadPartials from './partials'

// Bind simples
document.addEventListener('DOMContentLoaded', () => {
  LoadPartials()
})

// Bind axios
window.axios = axios
