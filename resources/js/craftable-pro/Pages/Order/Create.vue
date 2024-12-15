<template>
    <PageHeader sticky :title="$t('global', 'Create Order')">
        <Button
            :leftIcon="ArrowDownTrayIcon"
            @click="submit"
            :loading="form.processing"
            v-can="'global.order.create'"
        >
            {{ $t('global', 'Save') }}
        </Button>
    </PageHeader>

    <Form
        :form="form"
        :submit="submit"
        :orderItemsOptions="orderItemsOptions"
    />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import { Button, PageHeader } from 'craftable-pro/Components';
import { useForm } from 'craftable-pro/hooks/useForm';
import Form from './Form.vue';
import type { OrderForm } from './types';

interface Props {
    orderItemsOptions: Array<{ value: string | number; label: string }>;
}

const props = defineProps<Props>();

const { form, submit } = useForm<OrderForm>(
    {
        order_id: '',
        type: '',
        order_date: '',
        update_date: '',
        order_status: '',
        fulfillment: '',
        sales_channel: '',
        total_amount: '',
        total_currency: '',
        payment_method: '',
        buyer_name: '',
        address_line_1: '',
        address_line_2: '',
        city: '',
        state: '',
        postal_code: '',
        country_code: '',
        expected_ship_date: '',
        is_shipped: '',
        orderItems_ids: [],
    },
    route('craftable-pro.orders.store'),
    'post',
);
</script>
