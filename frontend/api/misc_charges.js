import { fetchJson } from '../utils/api'

export function listMiscCharges() {
    return fetchJson('/api/chargebacks')
}

export function getChargebackLookups() {
    return fetchJson('/api/chargebacks/lookups')
}

export function createMiscCharge(payload) {
    return fetchJson('/api/chargebacks', {
        method: 'POST',
        body: JSON.stringify(payload),
    })
}

export function updateMiscCharge(id, payload) {
    return fetchJson(`/api/chargebacks/${id}`, {
        method: 'PUT',
        body: JSON.stringify(payload),
    })
}

export function deleteMiscCharge(id) {
    return fetchJson(`/api/chargebacks/${id}`, { method: 'DELETE' })
}
