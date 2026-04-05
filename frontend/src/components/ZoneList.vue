
<script setup lang="ts">
import type { ZoneSummary } from './ZoneSummary.vue'
const zones: ZoneSummary[] = [
  {
    id: 1,
    name: 'Kamppi Center',
    type: 'commercial',
    status: 'active',
  },
  {
    id: 2,
    name: 'Esplanadi Park',
    type: 'street',
    status: 'active',
  },
]
</script>

<template>
  <section class="zone-list">
    <h1>Parking Zones</h1>
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
</style>