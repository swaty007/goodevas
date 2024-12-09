<template>
    <CraftableProUserPasswordPage v-bind="props" />
</template>

<script setup lang="ts">
import CraftableProUserPasswordPage from 'craftable-pro/Pages/CraftableProUser/Password/Edit.vue';
import type { CraftableProUser } from 'craftable-pro/types/models';

interface Props {
    craftableProUser: CraftableProUser;
}

const props = defineProps<Props>();
</script>
