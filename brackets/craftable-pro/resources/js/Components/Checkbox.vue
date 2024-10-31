<template>
    <div class="flex items-center">
        <input
            type="checkbox"
            class="block h-4 w-4 cursor-pointer rounded border dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-primary-600 dark:text-primary-500 focus:ring-0 focus:ring-transparent focus:ring-offset-0 focus-visible:ring-2 focus-visible:ring-primary-300 dark:focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800"
            :value="inputValue"
            v-model="model"
            :checked="checked"
            :indeterminate="indeterminate"
            :name="name"
            :id="name"
        />

        <label
            v-if="label"
            :for="name"
            class="ml-2 block cursor-pointer text-sm text-gray-900 dark:text-gray-100"
        >
            {{ label }}
        </label>
    </div>
</template>


<script setup lang="ts">
import { computed } from "vue";

interface Props {
  name?: string;
  label?: string;
  inputValue?: string | number;
  checked?: boolean;
  indeterminate?: boolean;
  modelValue?: boolean | Array<string | number>;
}

const props = withDefaults(defineProps<Props>(), {
  checked: false,
  indeterminate: false,
});

const emit = defineEmits(["update:modelValue"]);

const model = computed({
  get: () => props.modelValue,
  set: (newValue) => {
    emit("update:modelValue", newValue);
  },
});
</script>
