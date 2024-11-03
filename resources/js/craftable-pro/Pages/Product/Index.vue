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
      :baseUrl="route('craftable-pro.products.index')"
      :data="products"
      dataKey="products"
    >
      <template #bulkActions="{ bulkAction }">
        <Modal type="danger">
          <template #trigger="{ setIsOpen }">
            <Button
              @click="() => setIsOpen(true)"
              color="gray"
              variant="outline"
              size="sm"
              :leftIcon="TrashIcon"
              v-can="'global.product.destroy'"
            >
              {{ $t("global", "Delete") }}
            </Button>
          </template>

          <template #title>
            {{ $t("global", "Delete Product") }}
          </template>
          <template #content>
            {{
              $t(
                "global",
                "Are you sure you want to delete selected Product? All data will be permanently removed from our servers forever. This action cannot be undone."
              )
            }}
          </template>

          <template #buttons="{ setIsOpen }">
            <Button
              @click.prevent="
                () => {
                  bulkAction('post', route('craftable-pro.products.bulk-destroy'), {
                    onFinish: () => setIsOpen(false),
                  });
                }
              "
              color="danger"
              v-can="'global.product.destroy'"
            >
              {{ $t("global", "Delete") }}
            </Button>
            <Button
              @click.prevent="() => setIsOpen()"
              color="gray"
              variant="outline"
            >
              {{ $t("global", "Cancel") }}
            </Button>
          </template>
        </Modal>
      </template>
      <template #tableHead>
        
        <ListingHeaderCell sortBy="id">
            {{ $t("global", "Id") }}
        </ListingHeaderCell> 
        <ListingHeaderCell sortBy="ext_id">
            {{ $t("global", "Ext Id") }}
        </ListingHeaderCell> 
        <ListingHeaderCell sortBy="ean">
            {{ $t("global", "Ean") }}
        </ListingHeaderCell> 
        <ListingHeaderCell sortBy="additional_data">
            {{ $t("global", "Additional Data") }}
        </ListingHeaderCell> 
        <ListingHeaderCell sortBy="product_type_id">
            {{ $t("global", "Product Type Id") }}
        </ListingHeaderCell>
        <ListingHeaderCell>
          <span class="sr-only">{{ $t("global", "Actions") }}</span>
        </ListingHeaderCell>
      </template>
      <template #tableRow="{ item, action }: any">
        
        <ListingDataCell>
             {{ item.id }}
        </ListingDataCell> 
        <ListingDataCell>
             {{ item.ext_id }}
        </ListingDataCell> 
        <ListingDataCell>
             {{ item.ean }}
        </ListingDataCell> 
        <ListingDataCell>
             {{ item.additional_data }}
        </ListingDataCell> 
        <ListingDataCell>
             {{ item.product_type_id }}
        </ListingDataCell>
        <ListingDataCell>
          <div class="flex items-center justify-end gap-3">
            <IconButton
              :as="Link"
              :href="route('craftable-pro.products.edit', item)"
              variant="ghost"
              color="gray"
              :icon="PencilSquareIcon"
              v-can="'global.product.edit'"
            />

            <Modal type="danger">
              <template #trigger="{ setIsOpen }">
                <IconButton
                  @click="() => setIsOpen(true)"
                  color="gray"
                  variant="ghost"
                  :icon="TrashIcon"
                  v-can="'global.product.destroy'"
                />
              </template>

              <template #title>
                {{ $t("global", "Delete Product") }}
              </template>
              <template #content>
                {{
                  $t(
                    "global",
                    "Are you sure you want to delete selected Product? All data will be permanently removed from our servers forever. This action cannot be undone."
                  )
                }}
              </template>

              <template #buttons="{ setIsOpen }">
                <Button
                  @click.prevent="
                    () => {
                      action('delete', route('craftable-pro.products.destroy', item), {
                        onFinish: () => setIsOpen(false),
                      });
                    }
                  "
                  color="danger"
                  v-can="'global.product.destroy'"
                >
                  {{ $t("global", "Delete") }}
                </Button>
                <Button
                  @click.prevent="() => setIsOpen()"
                  color="gray"
                  variant="outline"
                >
                  {{ $t("global", "Cancel") }}
                </Button>
              </template>
            </Modal>
          </div>
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
    Publish,
} from "craftable-pro/Components";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { Product } from "./types";
import type { PageProps } from "craftable-pro/types/page";
import dayjs from "dayjs";



interface Props {
  products: PaginatedCollection<Product>;
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
