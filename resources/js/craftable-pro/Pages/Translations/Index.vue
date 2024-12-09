<template>
    <TranslationsPage v-bind="$props" />
</template>

<script lang="ts" setup>
import TranslationsPage from 'craftable-pro/Pages/Translations/Index.vue';
import type { LanguageLine } from 'craftable-pro/types/models';
import { PaginatedCollection } from 'craftable-pro/types/pagination';

interface Props {
    data: PaginatedCollection<LanguageLine>;
    groups: string[];
    locales: string[];
}

defineProps<Props>();
</script>
