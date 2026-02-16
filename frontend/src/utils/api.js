export async function fetchJson(url, opts = {}) {
    const res = await fetch(url, {
        method: opts.method || 'GET',
        headers: {
            'Content-Type': 'application/json',
            ...(opts.headers || {}),
        },
        body: opts.body,
    })

    if (!res.ok) {
        const txt = await res.text().catch(() => '')
        throw new Error(txt || `HTTP ${res.status}`)
    }

    const ct = res.headers.get('content-type') || ''
    return ct.includes('application/json') ? res.json() : res.text()
}
