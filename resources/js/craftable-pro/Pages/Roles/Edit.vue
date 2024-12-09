<template>
    <RoleEditPage v-bind="props" />
</template>

<script setup lang="ts">
import RoleEditPage from 'craftable-pro/Pages/Roles/Edit.vue';
import type { Role } from 'craftable-pro/types/models';

interface Props {
    role: Role;
    permissionsTree: any;
}

const props = defineProps<Props>();
</script>
