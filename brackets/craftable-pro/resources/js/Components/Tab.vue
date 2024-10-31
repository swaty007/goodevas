<template>
  <Tab as="template" :disabled="disabled" v-slot="{ selected }">
    <component
      :is="href ? Link : 'button'"
      :href="href"
      :class="[
        styles,
        (isExternallySelected && props.selected) ||
        (!isExternallySelected && selected)
          ? selectedStyles
          : unselectedStyles,
        disabled ? 'opacity-80 cursor-not-allowed' : '',
      ]"
      class="block px-5 py-3 text-sm font-medium leading-6 focus:outline-none"
      type="button"
    >
      <slot></slot>
    </component>
  </Tab>
</template>

<script setup lang="ts">
import { computed, inject } from "vue";
import { Tab } from "@headlessui/vue";
import { Link } from "@inertiajs/vue3";

interface Props {
  href?: string;
  selected?: boolean;
  disabled?: boolean;
  variant?: "underline" | "enclose";
}

const props = withDefaults(defineProps<Props>(), {
  selected: undefined,
});

const mergedProps = computed(() => {
  return {
    ...props,
    variant: props.variant || inject("tabGroupVariant", "enclose"),
  };
});

const isExternallySelected = computed(() => {
  return props.selected !== undefined;
});

const styles = computed(() => {
  switch (mergedProps.value.variant) {
    case "underline":
      return "border-b-2";
    case "enclose":
      return "rounded-t-md border border-b-0";
  }
});

const selectedStyles = computed(() => {
  switch (mergedProps.value.variant) {
    case "underline":
      return "border-b-primary-500 text-primary-600";
    case "enclose":
      return "relative z-10 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200";
  }
});

const unselectedStyles = computed(() => {
    switch (mergedProps.value.variant) {
        case "underline":
            if (props.disabled) return "border-transparent text-gray-500 dark:text-gray-400";
            return "border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500";
        case "enclose":
            if (props.disabled) return "border-transparent text-gray-500 dark:text-gray-400";
            return "border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200";
    }
});

</script>
