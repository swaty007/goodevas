<template>
    <Button
        class="relative"
        :color="isDarkMode ? 'primary' : 'warning'"
        variant="ghost"
        size="md"
        @click="toggleDarkMode"
    >
        <SunIcon
            v-if="!isDarkMode"
            class="h-5 w-5"
        />
        <MoonIcon
            v-if="isDarkMode"
            class="h-5 w-5"
        />
    </Button>
</template>

<script setup lang="ts">
import { SunIcon, MoonIcon } from "@heroicons/vue/24/solid";
import { Button } from "craftable-pro/Components";
import { onMounted, ref } from "vue";

const isDarkMode = ref(false);

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value
    setDarkMode(isDarkMode.value)
};

const setDarkMode = (darkMode: boolean) => {
    isDarkMode.value = darkMode;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    }
}

onMounted(() => {
    if (
        localStorage.theme === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        setDarkMode(true)
    } else {
        setDarkMode(false)
    }
});
</script>

<style scoped>
</style>
