<script setup>
import { ref, onMounted } from 'vue'

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL

const currentView = ref('client')

const message = ref('Quiero una cita mañana a las 8pm para 3 personas')
const loading = ref(false)
const chatResponse = ref(null)
const confirmResponse = ref(null)
const error = ref(null)

const rules = ref({
  business_start_time: '08:00',
  business_end_time: '18:00',
  max_people: 2,
  appointment_duration_minutes: 30,
  allow_autocorrection: true,
})

const logs = ref([])
const blockedDays = ref([])
const services = ref([])
const adminMessage = ref(null)

const newBlockedDay = ref({
  date: '',
  reason: 'Día bloqueado para demo',
})

const newService = ref({
  code: '',
  name: '',
  duration_minutes: 30,
  active: true,
})

async function sendMessage() {
  loading.value = true
  error.value = null
  confirmResponse.value = null

  try {
    const response = await fetch(`${apiBaseUrl}/chat`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: message.value }),
    })

    const data = await response.json()
    chatResponse.value = data

    if (!response.ok) {
      error.value = data.message || 'Error procesando la solicitud'
    }
  } catch (e) {
    error.value = 'No se pudo conectar con el backend'
  } finally {
    loading.value = false
  }
}

async function confirmAppointment() {
  if (!chatResponse.value?.proposal) return

  loading.value = true
  error.value = null

  try {
    const response = await fetch(`${apiBaseUrl}/appointments/confirm`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        customer_name: 'Francisco',
        customer_contact: 'francisco@test.com',
        ...chatResponse.value.proposal,
      }),
    })

    const data = await response.json()
    confirmResponse.value = data

    if (!response.ok) {
      error.value = data.message || 'No se pudo confirmar la cita'
    }

    await loadLogs()
  } catch (e) {
    error.value = 'No se pudo conectar con el backend'
  } finally {
    loading.value = false
  }
}

async function loadRules() {
  try {
    const response = await fetch(`${apiBaseUrl}/admin/rules`)
    const data = await response.json()

    if (data.data) {
      rules.value = {
        business_start_time: data.data.business_start_time?.slice(0, 5) || '08:00',
        business_end_time: data.data.business_end_time?.slice(0, 5) || '18:00',
        max_people: data.data.max_people,
        appointment_duration_minutes: data.data.appointment_duration_minutes,
        allow_autocorrection: Boolean(data.data.allow_autocorrection),
      }
    }
  } catch (e) {
    adminMessage.value = 'No se pudieron cargar las reglas'
  }
}

async function saveRules() {
  loading.value = true
  adminMessage.value = null

  try {
    const response = await fetch(`${apiBaseUrl}/admin/rules`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(rules.value),
    })

    const data = await response.json()

    if (!response.ok) {
      adminMessage.value = data.message || 'No se pudieron guardar las reglas'
      return
    }

    adminMessage.value = 'Reglas guardadas correctamente'
    await loadRules()
  } catch (e) {
    adminMessage.value = 'No se pudo conectar con el backend'
  } finally {
    loading.value = false
  }
}

async function loadLogs() {
  try {
    const response = await fetch(`${apiBaseUrl}/admin/guardrail-logs`)
    const data = await response.json()
    logs.value = data.data || []
  } catch (e) {
    adminMessage.value = 'No se pudieron cargar los logs'
  }
}

async function loadBlockedDays() {
  try {
    const response = await fetch(`${apiBaseUrl}/admin/blocked-days`)
    const data = await response.json()
    blockedDays.value = data.data || []
  } catch (e) {
    adminMessage.value = 'No se pudieron cargar los días bloqueados'
  }
}

async function saveBlockedDay() {
  if (!newBlockedDay.value.date) {
    adminMessage.value = 'Selecciona una fecha para bloquear'
    return
  }

  loading.value = true
  adminMessage.value = null

  try {
    const response = await fetch(`${apiBaseUrl}/admin/blocked-days`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newBlockedDay.value),
    })

    const data = await response.json()

    if (!response.ok) {
      adminMessage.value = data.message || 'No se pudo guardar el día bloqueado'
      return
    }

    adminMessage.value = 'Día bloqueado guardado'
    newBlockedDay.value = {
      date: '',
      reason: 'Día bloqueado para demo',
    }

    await loadBlockedDays()
  } catch (e) {
    adminMessage.value = 'No se pudo conectar con el backend'
  } finally {
    loading.value = false
  }
}

async function deleteBlockedDay(id) {
  loading.value = true
  adminMessage.value = null

  try {
    const response = await fetch(`${apiBaseUrl}/admin/blocked-days/${id}`, {
      method: 'DELETE',
    })

    const data = await response.json()

    if (!response.ok) {
      adminMessage.value = data.message || 'No se pudo eliminar el día bloqueado'
      return
    }

    adminMessage.value = 'Día bloqueado eliminado'
    await loadBlockedDays()
  } catch (e) {
    adminMessage.value = 'No se pudo conectar con el backend'
  } finally {
    loading.value = false
  }
}

async function loadServices() {
  try {
    const response = await fetch(`${apiBaseUrl}/admin/services`)
    const data = await response.json()
    services.value = data.data || []
  } catch (e) {
    adminMessage.value = 'No se pudieron cargar los servicios'
  }
}

async function saveService() {
  if (!newService.value.code || !newService.value.name) {
    adminMessage.value = 'Código y nombre del servicio son obligatorios'
    return
  }

  loading.value = true
  adminMessage.value = null

  try {
    const response = await fetch(`${apiBaseUrl}/admin/services`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newService.value),
    })

    const data = await response.json()

    if (!response.ok) {
      adminMessage.value = data.message || 'No se pudo guardar el servicio'
      return
    }

    adminMessage.value = 'Servicio guardado'
    newService.value = {
      code: '',
      name: '',
      duration_minutes: 30,
      active: true,
    }

    await loadServices()
  } catch (e) {
    adminMessage.value = 'No se pudo conectar con el backend'
  } finally {
    loading.value = false
  }
}

async function toggleService(service) {
  loading.value = true
  adminMessage.value = null

  try {
    const response = await fetch(`${apiBaseUrl}/admin/services/${service.id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name: service.name,
        duration_minutes: service.duration_minutes,
        active: !service.active,
      }),
    })

    const data = await response.json()

    if (!response.ok) {
      adminMessage.value = data.message || 'No se pudo actualizar el servicio'
      return
    }

    adminMessage.value = 'Servicio actualizado'
    await loadServices()
  } catch (e) {
    adminMessage.value = 'No se pudo conectar con el backend'
  } finally {
    loading.value = false
  }
}

async function deleteService(id) {
  loading.value = true
  adminMessage.value = null

  try {
    const response = await fetch(`${apiBaseUrl}/admin/services/${id}`, {
      method: 'DELETE',
    })

    const data = await response.json()

    if (!response.ok) {
      adminMessage.value = data.message || 'No se pudo eliminar el servicio'
      return
    }

    adminMessage.value = 'Servicio eliminado'
    await loadServices()
  } catch (e) {
    adminMessage.value = 'No se pudo conectar con el backend'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await loadRules()
  await loadLogs()
  await loadBlockedDays()
  await loadServices()
})
</script>

<template>
  <main class="page">
    <section class="card">
      <div class="topbar">
        <p class="badge">Guardrail Demo</p>

        <div class="tabs">
          <button :class="{ active: currentView === 'client' }" @click="currentView = 'client'">
            Cliente
          </button>
          <button :class="{ active: currentView === 'admin' }" @click="currentView = 'admin'">
            Admin
          </button>
        </div>
      </div>

      <template v-if="currentView === 'client'">
        <h1>Agendar cita con agente IA</h1>

        <p class="subtitle">
          El agente propone una cita, el guardrail corrige reglas inválidas y el usuario confirma.
        </p>

        <div class="box">
          <label>Mensaje</label>
          <textarea v-model="message" rows="4" />

          <button @click="sendMessage" :disabled="loading">
            {{ loading ? 'Procesando...' : 'Enviar al agente' }}
          </button>
        </div>

        <div v-if="error" class="alert error">
          {{ error }}
        </div>

        <div v-if="chatResponse" class="result">
          <h2>Respuesta del agente</h2>

          <p>{{ chatResponse.message }}</p>

          <p v-if="chatResponse.guardrail">
            <strong>Guardrail:</strong>
            <span :class="chatResponse.guardrail.decision">
              {{ chatResponse.guardrail.decision }}
            </span>
          </p>

          <pre v-if="chatResponse.proposal">{{ chatResponse.proposal }}</pre>

          <button
            v-if="chatResponse.requires_confirmation"
            class="confirm"
            @click="confirmAppointment"
            :disabled="loading"
          >
            Confirmar cita
          </button>
        </div>

        <div v-if="confirmResponse" class="result success">
          <h2>Confirmación</h2>
          <p>{{ confirmResponse.message }}</p>
          <pre>{{ confirmResponse }}</pre>
        </div>
      </template>

      <template v-if="currentView === 'admin'">
        <h1>Frontadmin de reglas</h1>

        <p class="subtitle">
          Cambia reglas, servicios, fechas bloqueadas y mira cómo el guardrail corrige el flujo.
        </p>

        <div v-if="adminMessage" class="alert neutral">
          {{ adminMessage }}
        </div>

        <div class="admin-grid">
          <div class="box panel">
            <h2>Reglas</h2>

            <label>Hora inicio</label>
            <input v-model="rules.business_start_time" type="time" />

            <label>Hora fin</label>
            <input v-model="rules.business_end_time" type="time" />

            <label>Máximo personas</label>
            <input v-model.number="rules.max_people" type="number" min="1" />

            <label>Duración cita minutos</label>
            <input v-model.number="rules.appointment_duration_minutes" type="number" min="5" />

            <label class="checkbox">
              <input v-model="rules.allow_autocorrection" type="checkbox" />
              Permitir autocorrección
            </label>

            <button @click="saveRules" :disabled="loading">
              Guardar reglas
            </button>
          </div>

          <div class="box panel">
            <h2>Servicios</h2>

            <label>Código</label>
            <input v-model="newService.code" type="text" placeholder="asesoria" />

            <label>Nombre</label>
            <input v-model="newService.name" type="text" placeholder="Asesoría" />

            <label>Duración minutos</label>
            <input v-model.number="newService.duration_minutes" type="number" min="5" />

            <label class="checkbox">
              <input v-model="newService.active" type="checkbox" />
              Activo
            </label>

            <button @click="saveService" :disabled="loading">
              Guardar servicio
            </button>

            <div v-if="services.length === 0" class="empty">
              No hay servicios.
            </div>

            <div v-for="service in services" :key="service.id" class="mini-item">
              <div>
                <strong>{{ service.name }}</strong>
                <p>{{ service.code }} · {{ service.duration_minutes }} min</p>
                <small>{{ service.active ? 'Activo' : 'Inactivo' }}</small>
              </div>

              <div class="actions">
                <button @click="toggleService(service)">
                  {{ service.active ? 'Desactivar' : 'Activar' }}
                </button>

                <button class="danger" @click="deleteService(service.id)">
                  Eliminar
                </button>
              </div>
            </div>
          </div>

          <div class="box panel">
            <h2>Días bloqueados</h2>

            <label>Fecha</label>
            <input v-model="newBlockedDay.date" type="date" />

            <label>Motivo</label>
            <input v-model="newBlockedDay.reason" type="text" />

            <button @click="saveBlockedDay" :disabled="loading">
              Bloquear día
            </button>

            <div v-if="blockedDays.length === 0" class="empty">
              No hay días bloqueados.
            </div>

            <div v-for="day in blockedDays" :key="day.id" class="mini-item">
              <div>
                <strong>{{ day.date }}</strong>
                <p>{{ day.reason || 'Sin motivo' }}</p>
              </div>

              <button class="danger" @click="deleteBlockedDay(day.id)">
                Eliminar
              </button>
            </div>
          </div>

          <div class="box panel logs">
            <div class="logs-header">
              <h2>Últimos logs</h2>
              <button @click="loadLogs">Refrescar</button>
            </div>

            <div v-if="logs.length === 0" class="empty">
              No hay logs todavía.
            </div>

            <div v-for="log in logs" :key="log.id" class="log-item">
              <strong :class="log.decision">{{ log.decision }}</strong>
              <p>{{ log.reason }}</p>
              <small>{{ log.created_at }}</small>

              <details>
                <summary>Ver payload</summary>
                <pre>{{ log }}</pre>
              </details>
            </div>
          </div>
        </div>
      </template>
    </section>
  </main>
</template>

<style>
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background: #f3f4f6;
  color: #111827;
}

.page {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 24px;
}

.card {
  width: 100%;
  max-width: 1200px;
  background: white;
  border-radius: 20px;
  padding: 28px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, .08);
}

.topbar {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  align-items: center;
}

.badge {
  display: inline-block;
  background: #eef2ff;
  color: #3730a3;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: bold;
}

.tabs {
  display: flex;
  gap: 8px;
}

.tabs button {
  background: #e5e7eb;
  color: #111827;
}

.tabs button.active {
  background: #111827;
  color: white;
}

h1 {
  margin: 16px 0 0;
  font-size: 32px;
}

h2 {
  margin: 0 0 12px;
}

.subtitle {
  color: #6b7280;
}

.box {
  display: grid;
  gap: 10px;
  margin-top: 22px;
}

.panel {
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 16px;
  background: #fafafa;
}

.admin-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.logs {
  grid-column: span 2;
  max-height: 640px;
  overflow-y: auto;
}

label {
  font-weight: bold;
}

textarea,
input {
  width: 100%;
  box-sizing: border-box;
  border: 1px solid #d1d5db;
  border-radius: 12px;
  padding: 12px;
  font-size: 16px;
}

.checkbox {
  display: flex;
  align-items: center;
  gap: 10px;
}

.checkbox input {
  width: auto;
}

button {
  border: 0;
  border-radius: 12px;
  padding: 12px 16px;
  background: #111827;
  color: white;
  font-weight: bold;
  cursor: pointer;
}

button:disabled {
  opacity: .6;
  cursor: not-allowed;
}

.confirm {
  background: #047857;
}

.danger {
  background: #b91c1c;
}

.actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.actions button {
  padding: 8px 12px;
}

.alert {
  margin-top: 16px;
  padding: 12px;
  border-radius: 12px;
}

.error {
  background: #fef2f2;
  color: #991b1b;
}

.neutral {
  background: #eff6ff;
  color: #1d4ed8;
}

.result {
  margin-top: 20px;
  padding: 16px;
  border-radius: 14px;
  border: 1px solid #e5e7eb;
  background: #fafafa;
}

.success {
  border-color: #a7f3d0;
  background: #ecfdf5;
}

.ALLOW {
  color: #047857;
  font-weight: bold;
}

.STEER {
  color: #b45309;
  font-weight: bold;
}

.BLOCK {
  color: #b91c1c;
  font-weight: bold;
}

.logs-header,
.mini-item {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: center;
}

.logs-header button,
.mini-item button {
  padding: 8px 12px;
}

.mini-item,
.log-item {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 12px;
  background: white;
}

.mini-item p,
.log-item p {
  margin: 4px 0;
}

.empty {
  color: #6b7280;
}

pre {
  background: #111827;
  color: white;
  padding: 14px;
  border-radius: 12px;
  overflow-x: auto;
}

@media (max-width: 800px) {
  .admin-grid {
    grid-template-columns: 1fr;
  }

  .logs {
    grid-column: span 1;
  }

  .topbar {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
