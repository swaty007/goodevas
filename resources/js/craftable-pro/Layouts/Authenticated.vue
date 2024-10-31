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
            class="hidden md:fixed md:inset-y-0 md:z-10 md:flex md:flex-col"
            :class="sidebarMiniMode ? 'md:w-24' : 'md:w-64'"
        >
            <Sidebar class="min-h-0" />
        </div>
        <div class="flex flex-1 flex-col" :class="sidebarMiniMode ? 'md:pl-24' : 'md:pl-64'">
            <div
                class="sticky top-0 z-40 bg-gray-100 dark:bg-gray-800 px-1 pt-1 md:px-3 flex items-center justify-between"
            >
                <button
                    type="button"
                    class="md:hidden -ml-0.5 -mt-0.5 inline-flex h-12 w-12 items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
                    @click="sidebarOpen = true"
                >
                    <span class="sr-only">{{ $t("craftable-pro", "Open sidebar") }}</span>
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>

                <button
                    type="button"
                    class="hidden md:inline-flex -ml-0.5 -mt-0.5 h-12 w-12 items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none"
                    @click="sidebarMiniMode = !sidebarMiniMode"
                >
                    <span class="sr-only">{{ $t("craftable-pro", "Open sidebar") }}</span>
                    <Bars3Icon class="h-6 w-6" aria-hidden="true" />
                </button>
                <div class="flex align-center">
                    <DarkMode />
                </div>
            </div>
            <main class="flex min-h-screen flex-1 flex-col">
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="translate-y-1 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="translate-y-1 opacity-0">
                    <div v-if="contentMounted">
                        <slot>
                            <div class="mx-auto max-w-screen-2xl px-4 sm:px-6 md:px-8">
                                <div class="flex h-96 items-center justify-center rounded-lg border-4 border-dashed border-gray-200 dark:border-gray-600 p-4">
                                    <span class="text-xl italic text-gray-300">
                                        {{ $t("craftable-pro", "Your content goes here...") }}
                                    </span>
                                </div>
                            </div>
                        </slot>
                    </div>
                </Transition>
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, provide, ref, watch } from "vue";
import { Dialog, DialogOverlay, TransitionChild, TransitionRoot, } from "@headlessui/vue";
import { Bars3Icon } from "@heroicons/vue/24/outline";
import { Sidebar } from "craftable-pro/Components";
import { handleFlashErrors } from "@/craftable-pro/hooks/handleFlashErrors";
import DarkMode from "@/craftable-pro/Components/DarkMode.vue";

const sidebarOpen = ref(false);
const sidebarMiniMode = ref(JSON.parse(localStorage.getItem('sidebarMiniMode') || 'false'));
provide('mini', sidebarMiniMode);

watch(sidebarMiniMode, (val) => {
    localStorage.setItem('sidebarMiniMode', JSON.stringify(val));
});

handleFlashErrors()

const contentMounted = ref(false)

onMounted(() => {
    contentMounted.value = true
})
</script>
