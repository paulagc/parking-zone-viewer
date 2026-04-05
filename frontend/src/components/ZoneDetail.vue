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
</script>
