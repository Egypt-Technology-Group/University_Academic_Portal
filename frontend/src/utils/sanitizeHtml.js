/**
 * HTML Sanitization Utility for Safe RichText Rendering
 * Strips dangerous tags (script, iframe, object, embed, form, on* handlers)
 * while preserving styling, formatting, typography, colors, links, lists, and tables.
 */

const ALLOWED_TAGS = new Set([
  'p', 'br', 'span', 'strong', 'b', 'em', 'i', 'u', 's', 'strike',
  'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
  'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'hr',
  'a', 'img', 'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td'
])

const ALLOWED_ATTRS = new Set([
  'href', 'target', 'rel', 'title', 'alt', 'src', 'class', 'style',
  'dir', 'lang', 'width', 'height', 'align', 'colspan', 'rowspan'
])

export function sanitizeHtml(dirtyHtml) {
  if (!dirtyHtml || typeof dirtyHtml !== 'string') return ''

  // In browser environment, use DOMParser for accurate, safe parsing and sanitization
  if (typeof window !== 'undefined' && window.DOMParser) {
    try {
      const parser = new DOMParser()
      const doc = parser.parseFromString(dirtyHtml, 'text/html')
      cleanNode(doc.body)
      return doc.body.innerHTML
    } catch (e) {
      console.warn('DOMParser sanitization failed, using regex fallback:', e)
    }
  }

  // Fallback regex sanitizer
  return dirtyHtml
    .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
    .replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, '')
    .replace(/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/gi, '')
    .replace(/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/gi, '')
    .replace(/on\w+="[^"]*"/gi, '')
    .replace(/on\w+='[^']*'/gi, '')
    .replace(/javascript:[^"']*/gi, '#')
}

function cleanNode(node) {
  const nodesToRemove = []

  for (let i = 0; i < node.childNodes.length; i++) {
    const child = node.childNodes[i]

    if (child.nodeType === 1) { // ELEMENT_NODE
      const tagName = child.tagName.toLowerCase()

      if (!ALLOWED_TAGS.has(tagName)) {
        nodesToRemove.push(child)
        continue
      }

      // Filter attributes
      const attrs = Array.from(child.attributes)
      for (const attr of attrs) {
        const attrName = attr.name.toLowerCase()

        // Remove event handlers (onclick, onload, onerror, etc.)
        if (attrName.startsWith('on') || !ALLOWED_ATTRS.has(attrName)) {
          child.removeAttribute(attr.name)
          continue
        }

        // Clean javascript: pseudo-protocol in links and images
        if (['href', 'src'].includes(attrName)) {
          const val = attr.value.trim().toLowerCase()
          if (val.startsWith('javascript:') || val.startsWith('data:text/html') || val.startsWith('vbscript:')) {
            child.removeAttribute(attr.name)
          }
          // Ensure external links have safe rel
          if (tagName === 'a' && attrName === 'href' && (val.startsWith('http://') || val.startsWith('https://'))) {
            child.setAttribute('rel', 'noopener noreferrer')
          }
        }
      }

      // Recursively clean children
      cleanNode(child)
    } else if (child.nodeType === 8) { // COMMENT_NODE
      nodesToRemove.push(child)
    }
  }

  for (const n of nodesToRemove) {
    node.removeChild(n)
  }
}
