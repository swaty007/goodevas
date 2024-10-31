<template>
  <aside
    class="pointer-events-auto absolute inset-y-0 right-0 w-screen max-w-sm 2xl:max-w-md pl-10 xl:relative xl:pl-0"
  >
    <div
      class="flex h-full flex-col overflow-y-auto border-l border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700"
      :class="{ 'px-4 py-5 sm:p-6': !noPadding }"
    >
      <slot />
    </div>
  </aside>
</template>

<script lang="ts" setup>
interface Props {
  noPadding?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  noPadding: false,
});
</script>
