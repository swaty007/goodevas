<template>
  <FormControl
    :name="name"
    :label="label"
    :error="error"
    :max-character-count="maxCharactersCount"
    :characters-count="charactersCount"
    :label-placement="labelPlacement"
  >
    <div class="relative rounded-md shadow-sm">
      <textarea
        v-model="value"
        :name="name"
        :id="name"
        :autocomplete="autocomplete"
        :class="[
          error
            ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
            : 'border-gray-300 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500',
        ]"
        class="block w-full rounded-md text-gray-800 dark:text-gray-100 dark:bg-gray-800 focus:outline-none sm:text-sm dark:border-gray-600"
        :placeholder="placeholder"
        :aria-invalid="!!error"
        :aria-describedby="error ? `${name}-error` : ''"
        :rows="rows"
        :required="required"
      />
      <div
        v-if="error"
        class="pointer-events-none absolute inset-y-0 right-0 top-3 items-center pr-3"
      >
        <ExclamationCircleIcon
          class="h-5 w-5 text-red-500"
          aria-hidden="true"
        />
      </div>
    </div>
  </FormControl>
</template>

<script setup lang="ts">
import { ExclamationCircleIcon } from "@heroicons/vue/20/solid";
import { computed } from "vue";
import { useInput } from "../hooks/useInput";
import { FormControl } from ".";

interface Props {
  name: string;
  label?: string;
  placeholder?: string;
  autocomplete?: string;
  rows?: number;
  required?: boolean;
  modelValue: string;
  maxCharactersCount?: number;
  labelPlacement?: "top" | "left";
}

const props = withDefaults(defineProps<Props>(), {
  rows: 5,
  required: false,
  modelValue: "",
  labelPlacement: "top",
});

const emit = defineEmits(["update:modelValue"]);
const { value, error } = useInput(props, emit);

const charactersCount = computed(() => {
  return value.value?.length;
});
</script>
