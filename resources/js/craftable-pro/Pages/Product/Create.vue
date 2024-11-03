<template>
  <PageHeader sticky :title="$t('global', 'Create Product')">
    <Button
      :leftIcon="ArrowDownTrayIcon"
      @click="submit"
      :loading="form.processing"
      v-can="'global.product.create'"
    >
      {{ $t("global", "Save") }}
    </Button>
  </PageHeader>

  <Form :form="form" :submit="submit" :productTypes="productTypes" />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PageHeader, Button } from "craftable-pro/Components";
import { useForm } from "craftable-pro/hooks/useForm";
import Form from "./Form.vue";
import type { ProductForm } from "./types";



interface Props {
  productTypes: Array<{ value: string | number, label: string }>
}

const props = defineProps<Props>();

const { form, submit } = useForm<ProductForm>(
    {
          ext_id: "",
ean: "",
additional_data: "",
product_type_id: ""
    },
    route("craftable-pro.products.store"),
    "post"
);
</script>
