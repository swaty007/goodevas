<template>
    <PageHeader
        sticky
        :title="$t('global', 'Edit Warehouse')"
        :subtitle="`Last updated at ${dayjs(warehouse.updated_at).format(
            'DD.MM.YYYY',
        )}`"
    >
        <Button
            :leftIcon="ArrowDownTrayIcon"
            @click="submit"
            :loading="form.processing"
            v-can="'global.warehouse.edit'"
        >
            {{ $t('global', 'Save') }}
        </Button>
    </PageHeader>

    <Form :form="form" :submit="submit" :defaultSettings="defaultSettings" />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import { Button, PageHeader } from 'craftable-pro/Components';
import { useForm } from 'craftable-pro/hooks/useForm';
import dayjs from 'dayjs';
import Form from './Form.vue';
import { Warehouse, WarehouseForm, WarehouseSettings } from './types';

interface Props {
    warehouse: Warehouse;
    defaultSettings: WarehouseSettings;
}

const props = defineProps<Props>();

const { form, submit } = useForm<WarehouseForm>(
    {
        name: props.warehouse?.name ?? '',
        country_id: props.warehouse?.country_id ?? '',
        virtual: !!props.warehouse?.country_id ?? '',
        ysell_name: props.warehouse?.ysell_name ?? '',
        settings: props.warehouse?.settings ?? {},
    },
    route('craftable-pro.warehouses.update', [props.warehouse?.id]),
);
</script>
