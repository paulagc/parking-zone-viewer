<script setup lang="ts">
import { onMounted, ref } from 'vue'
import ZoneDetailComponent from './ZoneDetail.vue'
import type { ZoneDetail } from './ZoneDetail.vue'
import type { ZoneSummary } from './ZoneSummary.vue'

const apiBaseUrl = (import.meta.env.VITE_BACKEND_URL ?? '').trim()

const zones = ref<ZoneSummary[]>([])
const isLoading = ref(true)
const errorMessage = ref('')

const selectedZoneId = ref<number | null>(null)
const selectedZone = ref<ZoneDetail | null>(null)
const isDetailLoading = ref(false)
const detailErrorMessage = ref('')
const searchTerm = ref('')

function getApiUrl(path: string): string {
  if (apiBaseUrl === '') {
    return path
  }

  return `${apiBaseUrl}${path}`
}

async function loadZoneDetail(zoneId: number): Promise<void> {
  selectedZoneId.value = zoneId
  isDetailLoading.value = true
  detailErrorMessage.value = ''

  try {
    const response = await fetch(getApiUrl(`/api/zones/${zoneId}`))

    if (!response.ok) {
      throw new Error(`Request failed with status ${response.status}`)
    }

    selectedZone.value = (await response.json()) as ZoneDetail
  } catch (error) {
    selectedZone.value = null
    detailErrorMessage.value = error instanceof Error ? error.message : 'Failed to load zone details'
  } finally {
    isDetailLoading.value = false
  }
}

async function loadZones(): Promise<void> {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const query = searchTerm.value.trim()
    const zonesPath = query === ''
      ? '/api/zones'
      : `/api/zones?q=${encodeURIComponent(query)}`

    const response = await fetch(getApiUrl(zonesPath))

    if (!response.ok) {
      throw new Error(`Request failed with status ${response.status}`)
    }

    const data = (await response.json()) as ZoneSummary[]
    zones.value = data

    if (selectedZoneId.value !== null && !data.some((zone) => zone.id === selectedZoneId.value)) {
      selectedZoneId.value = null
      selectedZone.value = null
    }

  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load zones'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  void loadZones()
})
</script>

<template>
  <section class="zone-list">
    <h1>Parking Zones</h1>
    <form class="search" @submit.prevent="void loadZones()">
      <label for="zone-search">Search by name</label>
      <div class="search-row">
        <input
          id="zone-search"
          v-model="searchTerm"
          type="search"
          placeholder="e.g. Kamppi"
        />
        <button type="submit">Search</button>
      </div>
    </form>
    <p v-if="isLoading">Loading zones...</p>
    <p v-else-if="errorMessage" class="error">Error: {{ errorMessage }}</p>
    <ul v-else>
      <li
        v-for="zone in zones"
        :key="zone.id"
        :class="{ selected: selectedZoneId === zone.id }"
        @click="void loadZoneDetail(zone.id)"
      >
        <ZoneDetailComponent
          v-if="selectedZoneId === zone.id"
          :zone="selectedZone"
          :is-loading="isDetailLoading"
          :error-message="detailErrorMessage"
        />
        <template v-else>
          <h2>{{ zone.name }}</h2>
          <p>Type: {{ zone.type }}</p>
          <p>Status: {{ zone.status }}</p>
        </template>
      </li>
    </ul>
    <p v-if="!isLoading && !errorMessage && zones.length === 0" class="empty">No zones found.</p>
  </section>
</template>

<style scoped>
.zone-list {
  max-width: 640px;
  margin: 2rem auto;
}

.zone-list h1 {
  margin-bottom: 1.5rem;
}

.search {
  display: grid;
  gap: 0.35rem;
  margin-bottom: 1rem;
}

.search input {
  border: 1px solid #dcdcdc;
  border-radius: 8px;
  padding: 0.6rem 0.7rem;
}

.search-row {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 0.5rem;
}

button {
  border: 1px solid #42b883;
  background: #42b883;
  color: #fff;
  border-radius: 8px;
  padding: 0.6rem 0.9rem;
  cursor: pointer;
}

ul {
  list-style: none;
  padding: 0;
  display: grid;
  gap: 0.75rem;
}

li {
  border: 1px solid #dcdcdc;
  border-radius: 10px;
  padding: 0.9rem 1rem;
  cursor: pointer;
}

li.selected {
  border-color: #42b883;
  background: #f4fbf8;
}

.error {
  color: #b00020;
}

.empty {
  color: #666;
}
</style>