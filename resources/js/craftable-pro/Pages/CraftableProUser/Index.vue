<template>
    <CraftableProUserPage v-bind="props" />
</template>

<script setup lang="ts">
import CraftableProUserPage from 'craftable-pro/Pages/CraftableProUser/Index.vue';
import type { CraftableProUser } from 'craftable-pro/types/models';
import { PaginatedCollection } from 'craftable-pro/types/pagination';

interface Props {
    craftableProUsers: PaginatedCollection<CraftableProUser>;
    filterOptions: {
        roles: string[];
    };
}

const props = defineProps<Props>();
</script>
