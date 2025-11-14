<template>
  <BaseEdge
    :id="id"
    :style="style"
    :path="path"
    :marker-end="markerEnd"
    :marker-start="markerStart"
    class="custom-edge"
  />
</template>

<script setup>
import { BaseEdge } from '@vue-flow/core'

defineProps({
  id: String,
  sourceX: Number,
  sourceY: Number,
  targetX: Number,
  targetY: Number,
  sourcePosition: String,
  targetPosition: String,
  style: Object,
  markerEnd: String,
  markerStart: String,
  path: String,
})
</script>

<style scoped>
.custom-edge {
  stroke: #b1b1b7;
  stroke-width: 2;
}

.custom-edge:hover {
  stroke: #3b82f6;
  stroke-width: 3;
}
</style>
