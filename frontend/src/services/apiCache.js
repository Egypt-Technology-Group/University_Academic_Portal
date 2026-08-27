/**
 * Client-Side In-Memory Cache & Request Deduplicator
 *
 * Accelerates public GET API requests with lightweight TTL caching
 * and in-flight promise deduplication to eliminate duplicate network calls and waterfalls.
 */

class ApiCache {
  constructor() {
    /** @type {Map<string, { data: any, expires: number }>} */
    this._cache = new Map()
    /** @type {Map<string, Promise<any>>} */
    this._inFlight = new Map()
  }

  /**
   * Execute a query with deduplication and in-memory TTL caching.
   *
   * @template T
   * @param {string} key
   * @param {() => Promise<T>} fetchFn
   * @param {number} [ttlMs=30000] - Cache TTL in milliseconds (default: 30s)
   * @param {boolean} [force=false]
   * @returns {Promise<T>}
   */
  async getOrFetch(key, fetchFn, ttlMs = 30000, force = false) {
    const now = Date.now()

    if (!force && this._cache.has(key)) {
      const entry = this._cache.get(key)
      if (entry.expires > now) {
        return entry.data
      }
      this._cache.delete(key)
    }

    if (!force && this._inFlight.has(key)) {
      return this._inFlight.get(key)
    }

    const promise = (async () => {
      try {
        const data = await fetchFn()
        this._cache.set(key, {
          data,
          expires: Date.now() + ttlMs,
        })
        return data
      } finally {
        this._inFlight.delete(key)
      }
    })()

    this._inFlight.set(key, promise)
    return promise
  }

  /**
   * Invalidate a specific cache key or keys matching a prefix.
   *
   * @param {string} [prefix]
   */
  invalidate(prefix = '') {
    if (!prefix) {
      this._cache.clear()
      return
    }
    for (const key of this._cache.keys()) {
      if (key.startsWith(prefix)) {
        this._cache.delete(key)
      }
    }
  }
}

export const apiCache = new ApiCache()
export default apiCache
