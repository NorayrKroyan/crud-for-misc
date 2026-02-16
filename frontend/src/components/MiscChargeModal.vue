<template>
  <div v-if="open" class="modalOverlay" @click.self="emit('close')">
    <div class="modalCard modalNarrow">
      <div class="modalHeader">
        <div class="modalTitle">Misc Charge / Credit</div>
        <button class="iconBtn" @click="emit('close')" aria-label="Close">✕</button>
      </div>

      <div class="modalBody">
        <div class="formColWrap">

          <div class="row">
            <div class="lbl">Date:</div>
            <div class="ctl">
              <input class="input inputSm" v-model="form.chargebackdate" type="date" />
            </div>
          </div>

          <div class="row">
            <div class="lbl">Credit:</div>
            <div class="ctl">
              <input
                  class="input inputSm moneyInput"
                  v-model="creditDisplay"
                  type="text"
                  inputmode="decimal"
                  @blur="creditDisplay = formatMoneyInput(creditDisplay)"
              />
            </div>
          </div>

          <div class="row">
            <div class="lbl">Debit:</div>
            <div class="ctl">
              <input
                  class="input inputSm moneyInput"
                  v-model="debitDisplay"
                  type="text"
                  inputmode="decimal"
                  @blur="debitDisplay = formatMoneyInput(debitDisplay)"
              />
            </div>
          </div>

          <div class="row">
            <div class="lbl">Description:</div>
            <div class="ctl">
              <input class="input" v-model.trim="form.description" type="text" maxlength="200" />
            </div>
          </div>

          <div class="row">
            <div class="lbl">Carrier:</div>
            <div class="ctl">
              <VSelect
                  class="vsel"
                  :options="carriers"
                  label="name"
                  :reduce="o => o.id"
                  v-model="form.carrier_id"
                  :clearable="true"
              />
            </div>
          </div>

          <div class="row">
            <div class="lbl">Source:</div>
            <div class="ctl">
              <VSelect
                  class="vsel"
                  :options="sources"
                  label="name"
                  :reduce="o => o.id"
                  v-model="form.source_id"
                  :clearable="true"
              />
            </div>
          </div>

        </div>

        <div v-if="error" class="err" style="margin-top:12px;">
          {{ error }}
        </div>
      </div>

      <div class="modalFooter">
        <div class="actions">
          <button
              v-if="!isNew"
              class="btnDanger"
              :disabled="saving"
              @click="onDelete"
          >
            Delete
          </button>
        </div>

        <div class="actions">
          <button
              class="btnPrimary"
              :disabled="saving || !canSave"
              @click="onSave"
          >
            {{ saving ? 'Saving…' : 'Save and return' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import VSelect from 'vue3-select'
import {
  createMiscCharge,
  updateMiscCharge,
  deleteMiscCharge,
} from '../utils/misc_charges.js'

const props = defineProps({
  open: { type: Boolean, default: false },
  row: { type: Object, default: null },
  carriers: { type: Array, default: () => [] },
  sources: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'saved'])

const saving = ref(false)
const error = ref('')

const form = reactive({
  idbill_chargebacks: null,
  chargebackdate: '',
  credit: 0,
  debit: 0,
  description: '',
  carrier_id: null,
  source_id: null,
})

const creditDisplay = ref('$0.00')
const debitDisplay = ref('$0.00')

const isNew = computed(() => !form.idbill_chargebacks)
const canSave = computed(() => {
  return Number.isFinite(parseMoney(creditDisplay.value)) &&
      Number.isFinite(parseMoney(debitDisplay.value))
})

watch(
    () => props.open,
    (v) => {
      if (!v) return
      error.value = ''

      const r = props.row
      if (!r) {
        Object.assign(form, {
          idbill_chargebacks: null,
          chargebackdate: '',
          credit: 0,
          debit: 0,
          description: '',
          carrier_id: null,
          source_id: null,
        })
        creditDisplay.value = '$0.00'
        debitDisplay.value = '$0.00'
        return
      }

      Object.assign(form, {
        idbill_chargebacks: r.idbill_chargebacks ?? null,
        chargebackdate: r.chargebackdate ?? '',
        credit: Number(r.credit ?? 0),
        debit: Number(r.debit ?? 0),
        description: r.description ?? '',
        carrier_id: r.carrier_id ?? null,
        source_id: r.source_id ?? null,
      })

      creditDisplay.value = formatMoneyInput(String(form.credit))
      debitDisplay.value = formatMoneyInput(String(form.debit))
    },
    { immediate: true }
)

function parseMoney(str) {
  if (str == null) return 0
  const cleaned = String(str).replace(/[^0-9.\-]/g, '')
  if (cleaned === '' || cleaned === '-' || cleaned === '.' || cleaned === '-.') return 0
  const n = Number(cleaned)
  return Number.isFinite(n) ? n : NaN
}

function formatMoneyInput(str) {
  const n = parseMoney(str)
  if (!Number.isFinite(n)) return '$0.00'
  return n.toLocaleString('en-US', { style: 'currency', currency: 'USD' })
}

function payload() {
  return {
    chargebackdate: form.chargebackdate || null,
    credit: parseMoney(creditDisplay.value),
    debit: parseMoney(debitDisplay.value),
    description: String(form.description || '').trim() || null,
    carrier_id: form.carrier_id || null,
    source_id: form.source_id || null,
  }
}

async function onSave() {
  saving.value = true
  error.value = ''
  try {
    const res = isNew.value
        ? await createMiscCharge(payload())
        : await updateMiscCharge(form.idbill_chargebacks, payload())

    emit('saved', res)
    emit('close')
  } catch (e) {
    error.value = e?.message || String(e)
  } finally {
    saving.value = false
  }
}

async function onDelete() {
  // ✅ NO confirm popup, delete immediately
  saving.value = true
  error.value = ''
  try {
    await deleteMiscCharge(form.idbill_chargebacks)
    emit('saved')
    emit('close')
  } catch (e) {
    error.value = e?.message || String(e)
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.formColWrap {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.row {
  display: grid;
  grid-template-columns: 150px 1fr;
  align-items: center;
  gap: 12px;
}

.lbl {
  text-align: right;
  font-weight: 900;
  font-size: 13px;
  color: #111827;
  white-space: nowrap;
}

.ctl {
  width: 100%;
}

/* narrower modal */
.modalNarrow {
  max-width: 520px;
}

/* slightly smaller inputs */
.inputSm {
  max-width: 260px;
}

/* right-align money inputs */
.moneyInput {
  text-align: right;
  font-variant-numeric: tabular-nums;
}
</style>
