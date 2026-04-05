<script setup lang="ts">
import type { ZoneDetail } from './ZoneDetail.vue'

export type ZoneSummary = Pick<ZoneDetail, 'id' | 'name' | 'type' | 'status'>
</script>