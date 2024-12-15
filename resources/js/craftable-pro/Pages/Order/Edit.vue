<template>
    <PageHeader
        sticky
        :title="$t('global', 'Edit Order')"
        :subtitle="`Last updated at ${dayjs(order.updated_at).format(
            'DD.MM.YYYY',
        )}`"
    >
        <Button
            :leftIcon="ArrowDownTrayIcon"
            @click="submit"
            :loading="form.processing"
            v-can="'global.order.edit'"
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
import dayjs from 'dayjs';
import Form from './Form.vue';
import type { Order, OrderForm } from './types';

interface Props {
    order: Order;
    orderItemsOptions: Array<{ value: string | number; label: string }>;
}

const props = defineProps<Props>();

const { form, submit } = useForm<OrderForm>(
    {
        order_id: props.order?.order_id ?? '',
        type: props.order?.type ?? '',
        order_date: props.order?.order_date ?? '',
        update_date: props.order?.update_date ?? '',
        order_status: props.order?.order_status ?? '',
        fulfillment: props.order?.fulfillment ?? '',
        sales_channel: props.order?.sales_channel ?? '',
        total_amount: props.order?.total_amount ?? '',
        total_currency: props.order?.total_currency ?? '',
        payment_method: props.order?.payment_method ?? '',
        buyer_name: props.order?.buyer_name ?? '',
        address_line_1: props.order?.address_line_1 ?? '',
        address_line_2: props.order?.address_line_2 ?? '',
        city: props.order?.city ?? '',
        state: props.order?.state ?? '',
        postal_code: props.order?.postal_code ?? '',
        country_code: props.order?.country_code ?? '',
        expected_ship_date: props.order?.expected_ship_date ?? '',
        is_shipped: props.order?.is_shipped ?? '',
        orderItems_ids: props.order?.items.map((item) => item.id) ?? [],
    },
    route('craftable-pro.orders.update', [props.order?.id]),
);
</script>
