<template>
  <button
    type="button"
    class="inline-flex items-center rounded bg-white dark:bg-gray-700 p-1 text-sm font-medium leading-4 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none"
    :class="{ 'bg-gray-200': active }"
    :title="title"
  >
    <component :is="icon" class="w-5" />
  </button>
</template>
<script setup lang="ts">
import type { Component } from "vue";

interface Props {
  icon: Component;
  active?: boolean;
  title?: string;
}

withDefaults(defineProps<Props>(), {
  active: false,
});
</script>
