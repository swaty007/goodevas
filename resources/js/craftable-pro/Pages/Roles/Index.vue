<template>
    <RoleIndexPage v-bind="props" />
</template>

<script setup lang="ts">
import RoleIndexPage from 'craftable-pro/Pages/Roles/Index.vue';
import type { Role } from 'craftable-pro/types/models';
import { PaginatedCollection } from 'craftable-pro/types/pagination';

interface Props {
    roles: PaginatedCollection<Role>;
}

const props = defineProps<Props>();
</script>
