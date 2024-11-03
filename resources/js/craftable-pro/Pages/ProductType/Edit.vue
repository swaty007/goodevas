<template>
  <PageHeader
    sticky
    :title="$t('global', 'Edit Product Type')"
    :subtitle="`Last updated at ${dayjs(
      productType.updated_at
    ).format('DD.MM.YYYY')}`"
  >
    <Button
      :leftIcon="ArrowDownTrayIcon"
      @click="submit"
      :loading="form.processing"
      v-can="'global.product-type.edit'"
    >
      {{ $t("global", "Save") }}
    </Button>
  </PageHeader>

  <Form :form="form" :submit="submit"  />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PageHeader, Button } from "craftable-pro/Components";
import { useForm } from "craftable-pro/hooks/useForm";
import Form from "./Form.vue";
import type { ProductType, ProductTypeForm } from "./types";
import dayjs from "dayjs";




interface Props {
  productType: ProductType;
  
}

const props = defineProps<Props>();

const { form, submit } = useForm<ProductTypeForm>(
    {
          name: props.productType?.name ?? ""
    },
    route("craftable-pro.product-types.update", [props.productType?.id])
);
</script>
