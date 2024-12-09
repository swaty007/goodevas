<template>
  <PageHeader :title="$t('global', 'Warehouses')">
    <Button
      :leftIcon="PlusIcon"
      :as="Link"
      :href="route('craftable-pro.warehouses.create')"
      v-can="'global.warehouse.create'"
    >
      {{ $t("global", "New Warehouse") }}
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
      :baseUrl="route('craftable-pro.warehouses.index')"
      :data="warehouses"
      dataKey="warehouses"
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
              v-can="'global.warehouse.destroy'"
            >
              {{ $t("global", "Delete") }}
            </Button>
          </template>

          <template #title>
            {{ $t("global", "Delete Warehouse") }}
          </template>
          <template #content>
            {{
              $t(
                "global",
                "Are you sure you want to delete selected Warehouse? All data will be permanently removed from our servers forever. This action cannot be undone."
              )
            }}
          </template>

          <template #buttons="{ setIsOpen }">
            <Button
              @click.prevent="
                () => {
                  bulkAction('post', route('craftable-pro.warehouses.bulk-destroy'), {
                    onFinish: () => setIsOpen(false),
                  });
                }
              "
              color="danger"
              v-can="'global.warehouse.destroy'"
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

        <ListingHeaderCell v-width-dragging sortBy="id">
            {{ $t("global", "Id") }}
        </ListingHeaderCell>
        <ListingHeaderCell v-width-dragging sortBy="name">
            {{ $t("global", "Name") }}
        </ListingHeaderCell>
        <ListingHeaderCell v-width-dragging sortBy="country_id">
            {{ $t("global", "Country Id") }}
        </ListingHeaderCell>
          <ListingHeaderCell v-width-dragging sortBy="virtual">
              {{ $t("global", "Virtual") }}
          </ListingHeaderCell>
          <ListingHeaderCell v-width-dragging sortBy="ysell_name">
              {{ $t("global", "Ysell Name") }}
          </ListingHeaderCell>
          <ListingHeaderCell v-width-dragging>
              {{ $t("global", "Settings") }}
          </ListingHeaderCell>
        <ListingHeaderCell v-width-dragging>
          <span class="sr-only">{{ $t("global", "Actions") }}</span>
        </ListingHeaderCell>
      </template>
      <template #tableRow="{ item, action }: any">

        <ListingDataCell>
             {{ item.id }}
        </ListingDataCell>
        <ListingDataCell>
             {{ item.name }}
        </ListingDataCell>
        <ListingDataCell>
             {{ item.country_id }}
        </ListingDataCell>
          <ListingDataCell>
              {{ item.virtual }}
          </ListingDataCell>
          <ListingDataCell>
              {{ item.ysell_name }}
          </ListingDataCell>
          <ListingDataCell>
              <pre>
                {{ item.settings }}
              </pre>
          </ListingDataCell>
        <ListingDataCell>
          <div class="flex items-center justify-end gap-3">
            <IconButton
              :as="Link"
              :href="route('craftable-pro.warehouses.edit', item)"
              variant="ghost"
              color="gray"
              :icon="PencilSquareIcon"
              v-can="'global.warehouse.edit'"
            />

            <Modal type="danger">
              <template #trigger="{ setIsOpen }">
                <IconButton
                  @click="() => setIsOpen(true)"
                  color="gray"
                  variant="ghost"
                  :icon="TrashIcon"
                  v-can="'global.warehouse.destroy'"
                />
              </template>

              <template #title>
                {{ $t("global", "Delete Warehouse") }}
              </template>
              <template #content>
                {{
                  $t(
                    "global",
                    "Are you sure you want to delete selected Warehouse? All data will be permanently removed from our servers forever. This action cannot be undone."
                  )
                }}
              </template>

              <template #buttons="{ setIsOpen }">
                <Button
                  @click.prevent="
                    () => {
                      action('delete', route('craftable-pro.warehouses.destroy', item), {
                        onFinish: () => setIsOpen(false),
                      });
                    }
                  "
                  color="danger"
                  v-can="'global.warehouse.destroy'"
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
import type { Warehouse } from "./types";
import type { PageProps } from "craftable-pro/types/page";
import dayjs from "dayjs";



interface Props {
  warehouses: PaginatedCollection<Warehouse>;
}
defineProps<Props>();
const downloadFile = () => {
    const url = window.location.href.split("?");
    if(url.length > 1) {
      window.location = route('craftable-pro.warehouses.export', url.pop()).slice(0, -1);
    } else {
      window.location = route('craftable-pro.warehouses.export');
    }
}
</script>
