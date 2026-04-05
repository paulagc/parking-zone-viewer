
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import type { ZoneSummary } from './ZoneSummary.vue'

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8888'

const zones = ref<ZoneSummary[]>([])
const isLoading = ref(true)
const errorMessage = ref('')

async function loadZones(): Promise<void> {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(`${apiBaseUrl}/api/zones`)

    if (!response.ok) {
      throw new Error(`Request failed with status ${response.status}`)
    }

    const data = (await response.json()) as ZoneSummary[]
    zones.value = data
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
    <p v-if="isLoading">Loading zones...</p>
    <p v-else-if="errorMessage" class="error">Error: {{ errorMessage }}</p>
    <ul>
      <li v-for="zone in zones" :key="zone.id">
        <h2>{{ zone.name }}</h2>
        <p>Type: {{ zone.type }}</p>
        <p>Status: {{ zone.status }}</p>
      </li>
    </ul>
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
}

.error {
  color: #b00020;
}
</style>