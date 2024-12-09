<template>
    <PageHeader sticky :title="$t('global', 'Create Product Type')">
        <Button
            :leftIcon="ArrowDownTrayIcon"
            @click="submit"
            :loading="form.processing"
            v-can="'global.product-type.create'"
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
import Form from './Form.vue';
import type { ProductTypeForm } from './types';

interface Props {}

const props = defineProps<Props>();

const { form, submit } = useForm<ProductTypeForm>(
    {
        name: '',
    },
    route('craftable-pro.product-types.store'),
    'post',
);
</script>
