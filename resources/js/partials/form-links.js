/**
 * Makes <a>-tags with a `data-submit` attribute submit the given form on click.
 */

export default () => {
  document.querySelectorAll('a[data-submit]').forEach(bind)
}

/**
 * Submits the form the node refers to
 * @param {Element} node
 */
const bind = (node) => {
  // Find the form
  const form = document.querySelector(`#${node.dataset.submit}`)

  // Fail if missing
  if (!form) {
    return
  }

  // Prevent double submitting
  let submitted = false

  // Bind to click
  node.addEventListener('click', event => {
    event.preventDefault()

    // Submit just once
    if (submitted) {
      return
    }

    submitted = true
    form.submit()
    node.setAttribute('disabled', true)
  })
}
