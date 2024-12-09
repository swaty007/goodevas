<template>
    <CraftableProUserProfilePage v-bind="props" />
</template>

<script setup lang="ts">
import CraftableProUserProfilePage from 'craftable-pro/Pages/CraftableProUser/Profile/Edit.vue';
import type { CraftableProUser } from 'craftable-pro/types/models';

interface Props {
    craftableProUser: CraftableProUser;
    locales?: string[];
}

const props = defineProps<Props>();
</script>
