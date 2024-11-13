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
    <Listing
      :baseUrl="route('craftable-pro.products.index-api')"
      :data="productsApi"
      dataKey="productsApi"
      :with-bulk-select="false"
    >
      <template #tableHead>

        <ListingHeaderCell v-for="(field, key) in productsApi?.data[0]" :sort-by="key">
            {{ key }}
        </ListingHeaderCell>
        <ListingHeaderCell>
          <span class="sr-only">{{ $t("global", "Actions") }}</span>
        </ListingHeaderCell>
      </template>
      <template #tableRow="{ item, action }: any">
        <ListingDataCell v-for="(field, key) in productsApi?.data[0]">
            <Avatar
                v-if="key === 'image'"
                :src="field"
                name="Logo"
            />
             <pre v-else-if="key === 'warehouseStock'">
                 {{ field }}
             </pre>
            <template v-else>
                {{ field }}
            </template>
        </ListingDataCell>
      </template>
    </Listing>
  </PageContent>
</template>

<script setup lang="ts">
import { Link, usePage } from "@inertiajs/vue3";
import {
    PlusIcon,
    TrashIcon,
    PencilSquareIcon,
    ArrowDownTrayIcon,
} from "@heroicons/vue/24/outline";
import {
    PageHeader,
    PageContent,
    Button,
    Listing,
    Avatar,
    ListingHeaderCell,
    ListingDataCell,
    Modal,
    Multiselect,
    IconButton,
    FiltersDropdown,
    Publish, TextInput,
} from "craftable-pro/Components";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { Product } from "./types";
import type { PageProps } from "craftable-pro/types/page";
import dayjs from "dayjs";
import { Warehouse } from "@/craftable-pro/Pages/Warehouse/types";
import { useAction } from "craftable-pro/hooks/useAction";
import debounce from "lodash/debounce";



interface Props {
  productsApi: object[];
  warehouses: Warehouse[]
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
