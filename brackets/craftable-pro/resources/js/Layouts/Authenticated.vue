<template>
  <div>
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog
        as="div"
        class="fixed inset-0 z-40 flex md:hidden"
        @close="sidebarOpen = false"
      >
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <DialogOverlay class="fixed inset-0 bg-gray-600 bg-opacity-75" />
        </TransitionChild>
        <TransitionChild
          as="div"
          class="flex flex-1"
          enter="transition ease-in-out duration-300 transform"
          enter-from="-translate-x-full"
          enter-to="translate-x-0"
          leave="transition ease-in-out duration-300 transform"
          leave-from="translate-x-0"
          leave-to="-translate-x-full"
        >
          <Sidebar class="relative w-full max-w-xs" />
        </TransitionChild>
        <div class="w-14 flex-shrink-0">
          <!-- Force sidebar to shrink to fit close icon -->
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Static sidebar for desktop -->
    <div
      class="hidden md:fixed md:inset-y-0 md:z-10 md:flex md:w-64 md:flex-col"
    >
      <Sidebar class="min-h-0" />
    </div>
    <div class="flex flex-1 flex-col md:pl-64">
      <div
        class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-800 pl-1 pt-1 md:hidden md:pl-3 md:pt-3"
      >
        <button
          type="button"
          class="-ml-0.5 -mt-0.5 inline-flex h-12 w-12 items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
          @click="sidebarOpen = true"
        >
          <span class="sr-only">{{ $t("craftable-pro", "Open sidebar") }}</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>
      </div>
      <main class="flex min-h-screen flex-1 flex-col">
        <slot>
          <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 md:px-8">
            <div
              class="flex h-96 items-center justify-center rounded-lg border-4 border-dashed border-gray-200 dark:border-gray-600 p-4"
            >
              <span class="text-xl italic text-gray-300">
                {{ $t("craftable-pro", "Your content goes here...") }}
              </span>
            </div>
          </div>
        </slot>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import {
  Dialog,
  DialogOverlay,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { Bars3Icon } from "@heroicons/vue/24/outline";
import { Sidebar } from "craftable-pro/Components";

const sidebarOpen = ref(false);
</script>
