<template>
    <PageHeader
        sticky
        :title="$t('global', 'Edit Product')"
        :subtitle="`Last updated at ${dayjs(product.updated_at).format(
            'DD.MM.YYYY',
        )}`"
    >
        <Button
            :leftIcon="ArrowDownTrayIcon"
            @click="submit"
            :loading="form.processing"
            v-can="'global.product.edit'"
        >
            {{ $t('global', 'Save') }}
        </Button>
    </PageHeader>

    <Form :form="form" :submit="submit" :productTypes="productTypes" />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import { Button, PageHeader } from 'craftable-pro/Components';
import { useForm } from 'craftable-pro/hooks/useForm';
import dayjs from 'dayjs';
import Form from './Form.vue';
import type { Product, ProductForm } from './types';

interface Props {
    product: Product;
    productTypes: Array<{ value: string | number; label: string }>;
}

const props = defineProps<Props>();

const { form, submit } = useForm<ProductForm>(
    {
        ext_id: props.product?.ext_id ?? '',
        ean: props.product?.ean ?? '',
        additional_data: props.product?.additional_data ?? '',
        product_type_id: props.product?.product_type_id ?? '',
    },
    route('craftable-pro.products.update', [props.product?.id]),
);
</script>
