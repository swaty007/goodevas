<template>
  <div :class="['bg-white', 'dark:bg-gray-700', 'shadow', 'sm:rounded-md', $attrs.class]">
    <TabGroup v-if="hasTabs">
      <slot name="header" :title="title">
        <CardHeader
          v-if="hasActions || title || hasTabs"
          class="flex items-center justify-between"
        >
          <div class="flex flex-col gap-3">
            <div v-if="title" class="flex items-center">
              <h3 class="font-medium leading-6">
                {{ title }}
              </h3>
            </div>
            <TabList
              class="-mb-2 flex gap-2"
              :class="[
                title
                  ? 'sm:-mb-4 [&>*]:translate-y-px'
                  : 'sm:-mb-[calc(1.25rem+1px)]',
              ]"
            >
              <slot name="tabs"></slot>
            </TabList>
          </div>
          <!-- Spacer to help align everything to one height -->
          <div class="h-9" />
          <div class="flex items-center gap-4">
            <slot name="actions" />
          </div>
        </CardHeader>
      </slot>

      <CardContent
        :noPadding="noPadding"
        :class="{
          ' sm:rounded-t-md': !hasHeader && !hasActions && !title && !hasTabs,
          ' sm:rounded-b-md': !hasFooter,
        }"
      >
        <slot />
      </CardContent>
      <slot name="footer" />
    </TabGroup>
    <template v-else>
      <slot name="header" :title="title">
        <CardHeader
          v-if="hasActions || title"
          class="flex items-center justify-between"
        >
          <div class="flex flex-col gap-3">
            <div v-if="title" class="flex items-center">
              <h3 class="font-medium leading-6">
                {{ title }}
              </h3>
            </div>
          </div>
          <!-- Spacer to help align everything to one height -->
          <div class="h-9" />
          <div class="flex items-center gap-4">
            <slot name="actions" />
          </div>
        </CardHeader>
      </slot>

      <CardContent
        :noPadding="noPadding"
        :class="{
          ' sm:rounded-t-md': !hasHeader && !hasActions && !title,
          ' sm:rounded-b-md': !hasFooter,
        }"
      >
        <slot />
      </CardContent>
      <slot name="footer" />
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, useSlots } from "vue";
import CardHeader from "./CardHeader.vue";
import CardContent from "./CardContent.vue";
import { TabGroup, TabList } from "@headlessui/vue";

interface Props {
  title?: string;
  noPadding?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  noPadding: false,
});

const slots = useSlots();
const hasHeader = computed(() => !!slots.header);
const hasActions = computed(() => !!slots.actions);
const hasFooter = computed(() => !!slots.footer);
const hasTabs = computed(() => !!slots.tabs);
</script>
