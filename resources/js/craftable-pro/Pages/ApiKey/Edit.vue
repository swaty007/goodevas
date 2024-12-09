<template>
    <PageHeader
        sticky
        :title="$t('global', 'Edit Api Key')"
        :subtitle="`Last updated at ${dayjs(apiKey.updated_at).format(
            'DD.MM.YYYY',
        )}`"
    >
        <Button
            :leftIcon="ArrowDownTrayIcon"
            @click="submit"
            :loading="form.processing"
            v-can="'global.api-key.edit'"
        >
            {{ $t('global', 'Save') }}
        </Button>
    </PageHeader>

    <Form :form="form" :submit="submit" />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import { Button, PageHeader } from 'craftable-pro/Components';
import { useForm } from 'craftable-pro/hooks/useForm';
import dayjs from 'dayjs';
import Form from './Form.vue';
import type { ApiKey, ApiKeyForm } from './types';

interface Props {
    apiKey: ApiKey;
}

const props = defineProps<Props>();

const { form, submit } = useForm<ApiKeyForm>(
    {
        name: props.apiKey?.name ?? '',
        type: props.apiKey?.type ?? '',
        key: props.apiKey?.key ?? {},
        additional_data: props.apiKey?.additional_data ?? {},
    },
    route('craftable-pro.api-keys.update', [props.apiKey?.id]),
);
</script>
