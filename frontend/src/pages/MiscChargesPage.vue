<template>
  <div class="container">
    <div class="pageHeader">
      <p class="pageTitle">Misc Charges & Credits</p>
      <button class="btnPrimary" @click="openNew">New Entry</button>
    </div>

    <div v-if="err" class="err">{{ err }}</div>

    <div class="card">
      <div class="dtControls">
        <div class="dtLeft">
          <span>Show</span>
          <select class="dtSelect" v-model.number="pageSize" @change="page=1">
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <span>entries</span>
        </div>

        <div class="dtRight">
          <label class="dtSearchLabel">Search:</label>
          <input class="dtSearchInput" v-model.trim="q" />
        </div>
      </div>

      <table class="table striped compact">
        <thead>
        <tr>
          <th>Description</th>
          <th style="width:120px; ">Credit</th>
          <th style="width:120px; ">Debit</th>
          <th style="width:220px;">Carrier</th>
          <th style="width:220px;">Source</th>
          <th style="width:120px;">Date</th>
        </tr>
        </thead>

        <tbody>
        <tr v-for="r in paged" :key="r.idbill_chargebacks">
          <td>
            <a href="#" class="link" @click.prevent="openEdit(r)">
              {{ r.description || '' }}
            </a>
          </td>
          <td class="num">{{ money(r.credit) }}</td>
          <td class="num">{{ money(r.debit) }}</td>
          <td>{{ r.carrier_name || '' }}</td>
          <td>{{ r.source_name || '' }}</td>
          <td>
              {{ formatDate(r.chargebackdate) }}
          </td>
        </tr>

        <tr v-if="paged.length === 0">
          <td colspan="6" class="empty">No entries found.</td>
        </tr>
        </tbody>
      </table>

      <div class="dtFooter">
        <div class="dtInfo">
          Showing {{ startRow }} to {{ endRow }} of {{ filtered.length }}
        </div>

        <div class="dtPager">
          <button class="dtPagerBtn" :disabled="page <= 1" @click="page--">Previous</button>
          <button class="dtPagerBtn" :disabled="page >= totalPages" @click="page++">Next</button>
        </div>
      </div>
    </div>

    <MiscChargeModal
        :open="modalOpen"
        :row="selected"
        :carriers="carriers"
        :sources="sources"
        @close="modalOpen=false"
        @saved="onChanged"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import MiscChargeModal from '../components/MiscChargeModal.vue'
import { listMiscCharges, getChargebackLookups } from '../utils/misc_charges.js'

const rows = ref([])
const carriers = ref([])
const sources = ref([])

const err = ref('')
const q = ref('')

const page = ref(1)
const pageSize = ref(25)

const modalOpen = ref(false)
const selected = ref(null)

onMounted(() => reload())
watch(q, () => page.value = 1)

function formatDate(val) {
  if (!val) return ''
  const d = new Date(val)
  if (isNaN(d)) return val
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  const yyyy = d.getFullYear()
  return `${mm}/${dd}/${yyyy}`
}

function money(v) {
  const n = Number(v || 0)
  return n.toLocaleString('en-US', { style: 'currency', currency: 'USD' })
}

async function reload() {
  err.value = ''
  try {
    const [data, lk] = await Promise.all([
      listMiscCharges(),
      getChargebackLookups(),
    ])

    rows.value = Array.isArray(data) ? data : (data?.data ?? [])
    carriers.value = Array.isArray(lk?.carriers) ? lk.carriers : []
    sources.value = Array.isArray(lk?.sources) ? lk.sources : []
  } catch (e) {
    err.value = e?.message || String(e)
  }
}

function openEdit(r) {
  selected.value = r
  modalOpen.value = true
}

function openNew() {
  selected.value = null
  modalOpen.value = true
}

const filtered = computed(() => {
  if (!q.value) return rows.value
  const s = q.value.toLowerCase()
  return rows.value.filter(r => JSON.stringify(r).toLowerCase().includes(s))
})

const totalPages = computed(() =>
    Math.max(1, Math.ceil(filtered.value.length / pageSize.value))
)

const paged = computed(() => {
  const start = (page.value - 1) * pageSize.value
  return filtered.value.slice(start, start + pageSize.value)
})

const startRow = computed(() =>
    filtered.value.length ? (page.value - 1) * pageSize.value + 1 : 0
)

const endRow = computed(() =>
    Math.min(page.value * pageSize.value, filtered.value.length)
)

async function onChanged() {
  modalOpen.value = false
  await reload()
}
</script>
