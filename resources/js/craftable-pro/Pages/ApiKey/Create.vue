<template>
    <PageHeader sticky :title="$t('global', 'Create Api Key')">
        <Button
            :leftIcon="ArrowDownTrayIcon"
            @click="submit"
            :loading="form.processing"
            v-can="'global.api-key.create'"
        >
            {{ $t('global', 'Save') }}
        </Button>
    </PageHeader>

    <Form :form="form" :submit="submit" :types="types" />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import { Button, PageHeader } from 'craftable-pro/Components';
import { useForm } from 'craftable-pro/hooks/useForm';
import Form from './Form.vue';
import type { ApiKeyForm } from './types';

interface Props {
    types: string[];
}

const props = defineProps<Props>();

const { form, submit } = useForm<ApiKeyForm>(
    {
        name: '',
        type: '',
        key: {},
        additional_data: {},
    },
    route('craftable-pro.api-keys.store'),
    'post',
);
</script>
