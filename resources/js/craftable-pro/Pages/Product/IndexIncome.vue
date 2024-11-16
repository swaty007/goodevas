<template>
  <PageHeader :title="$t('global', 'Products')">
    <Button
      :leftIcon="PlusIcon"
      :as="Link"
      :href="route('craftable-pro.products.create')"
      v-can="'global.product.create'"
    >
      {{ $t("global", "New Product") }}
    </Button>
    <Button
      :leftIcon="ArrowDownTrayIcon"
      as="a"
      class="ml-2"
      @click="downloadFile"
    >
      {{ $t("global", "Export") }}
    </Button>
  </PageHeader>

  <PageContent>
      <ProductTable
          :base-url="route('craftable-pro.products.index-income')"
          :products="products"
          :warehouses="warehouses"
          :income="true"
      />
  </PageContent>
</template>

<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import {
    PlusIcon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/outline";
import {
    PageHeader,
    PageContent,
    Button,
} from "craftable-pro/Components";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { Product } from "./types";
import { Warehouse } from "@/craftable-pro/Pages/Warehouse/types";
import ProductTable from "@/craftable-pro/Components/Product/ProductTable.vue";

interface Props {
  products: PaginatedCollection<Product>;
  warehouses: Object<string, Warehouse>;
}
defineProps<Props>();
const downloadFile = () => {
    const url = window.location.href.split("?");
    if(url.length > 1) {
      window.location = route('craftable-pro.products.export', url.pop()).slice(0, -1);
    } else {
      window.location = route('craftable-pro.products.export');
    }
}
</script>
