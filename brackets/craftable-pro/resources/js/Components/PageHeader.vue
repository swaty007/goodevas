<template>
  <div class="w-full">
    <Head v-if="title" :title="title" />

    <header
      class="w-full border-b border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 sm:px-6"
      :class="{
        'sticky top-0 z-30': sticky,
        'py-4': subtitle,
        'py-5': !subtitle,
      }"
    >
      <div class="flex flex-wrap items-center justify-between sm:flex-nowrap">
        <!-- Spacer -->
        <div class="h-10" />
        <div class="mr-auto">
          <h3 v-if="title" class="text-xl font-medium leading-6 text-gray-900 dark:text-gray-100">
            {{ title }}
          </h3>
          <p v-if="subtitle" class="mt-1 text-sm text-gray-500 dark:text-gray-300">
            {{ subtitle }}
          </p>
        </div>
        <div class="flex flex-wrap gap-1">
          <slot />
        </div>
      </div>
    </header>
  </div>
</template>

<script setup lang="ts">
import { Head } from "@inertiajs/vue3";

interface Props {
  title: string;
  subtitle?: string;
  sticky?: boolean;
}

withDefaults(defineProps<Props>(), {
  sticky: false,
});
</script>
