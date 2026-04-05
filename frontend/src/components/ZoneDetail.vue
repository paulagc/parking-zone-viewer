<script setup lang="ts">
export type ZoneDetail = {
  id: number
  name: string
  type: string
  status: string
  description: string
  max_capacity: number
  hourly_rate_eur: number
  latitude: number
  longitude: number
  amenities: string[]
  opening_hours: {
    weekdays: string
    weekends: string
  }
}

const props = defineProps<{
  zone: ZoneDetail | null
  isLoading: boolean
  errorMessage: string
}>()
</script>

<template>
  <section class="zone-detail">
    <p v-if="props.isLoading">Loading details...</p>
    <p v-else-if="props.errorMessage" class="error">Error: {{ props.errorMessage }}</p>
    <p v-else-if="!props.zone" class="placeholder">Select a zone to view details.</p>

    <div v-else>
      <h2>{{ props.zone.name }}</h2>
      <p><strong>Type:</strong> {{ props.zone.type }}</p>
      <p><strong>Status:</strong> {{ props.zone.status }}</p>
      <p><strong>Description:</strong> {{ props.zone.description }}</p>
      <p><strong>Max capacity:</strong> {{ props.zone.max_capacity }}</p>
      <p><strong>Hourly rate:</strong> {{ props.zone.hourly_rate_eur.toFixed(2) }} €</p>
      <p><strong>Latitude:</strong> {{ props.zone.latitude }}</p>
      <p><strong>Longitude:</strong> {{ props.zone.longitude }}</p>
      <p><strong>Opening hours:</strong> Weekdays {{ props.zone.opening_hours.weekdays }}, Weekends {{ props.zone.opening_hours.weekends }}</p>

      <div>
        <strong>Amenities:</strong>
        <ul>
          <li v-for="amenity in props.zone.amenities" :key="amenity">{{ amenity }}</li>
        </ul>
      </div>
    </div>
  </section>
</template>

<style scoped>
.zone-detail {
  border: 1px solid #dcdcdc;
  border-radius: 10px;
  padding: 1rem;
}

.placeholder {
  color: #666;
}

.error {
  color: #b00020;
}

ul {
  margin: 0.5rem 0 0;
}
</style>
