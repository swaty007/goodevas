<template>
  <Card class="xl:col-span-2">
    <div class="flex items-center justify-between -my-1">
      <h3 class="font-medium leading-6">
        {{ $t("craftable-pro", "Translation") }}
      </h3>

      <div class="flex items-center divide-x-2">
        <Tooltip v-for="locale in availableLocales" position="top">
          <template #button>
            <button
              @click="currentLocale = locale"
              class="px-3 text-sm uppercase leading-5 flex items-center hover:cursor-pointer min-w-[72px]"
              :class="[
                locale === currentLocale
                  ? 'font-semibold text-indigo-600'
                  : 'font-normal text-slate-500 hover:text-slate-900 dark:hover:text-slate-100',
              ]"
            >
              <LocaleFlag :locale="locale" />
            </button>
          </template>
          <template #content> {{ locale }} </template>
        </Tooltip>
      </div>
    </div>
  </Card>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Card, Tooltip, LocaleFlag } from "craftable-pro/Components";
import { useFormLocale } from "craftable-pro/hooks/useFormLocale";

const props = defineProps(["modelValue"]);
const emit = defineEmits(["update:modelValue"]);

const currentLocale = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit("update:modelValue", value);
  },
});

const { availableLocales } = useFormLocale();
</script>
