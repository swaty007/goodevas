<template>
  <PageHeader :title="$t('global', 'Ysells')">
    <Button
      :leftIcon="PlusIcon"
      :as="Link"
      :href="route('craftable-pro.ysells.create')"
      v-can="'global.ysell.create'"
    >
      {{ $t("global", "New Ysell") }}
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
      :baseUrl="route('craftable-pro.ysells.index')"
      :data="ysells"
      dataKey="ysells"
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
              v-can="'global.ysell.destroy'"
            >
              {{ $t("global", "Delete") }}
            </Button>
          </template>

          <template #title>
            {{ $t("global", "Delete Ysell") }}
          </template>
          <template #content>
            {{
              $t(
                "global",
                "Are you sure you want to delete selected Ysell? All data will be permanently removed from our servers forever. This action cannot be undone."
              )
            }}
          </template>

          <template #buttons="{ setIsOpen }">
            <Button
              @click.prevent="
                () => {
                  bulkAction('post', route('craftable-pro.ysells.bulk-destroy'), {
                    onFinish: () => setIsOpen(false),
                  });
                }
              "
              color="danger"
              v-can="'global.ysell.destroy'"
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
        <ListingHeaderCell v-width-dragging sortBy="api_key">
            {{ $t("global", "Api Key") }}
        </ListingHeaderCell>
        <ListingHeaderCell v-width-dragging sortBy="name">
            {{ $t("global", "Name") }}
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
             {{ item.api_key }}
        </ListingDataCell>
        <ListingDataCell>
             {{ item.name }}
        </ListingDataCell>
        <ListingDataCell>
          <div class="flex items-center justify-end gap-3">
            <IconButton
              :as="Link"
              :href="route('craftable-pro.ysells.edit', item)"
              variant="ghost"
              color="gray"
              :icon="PencilSquareIcon"
              v-can="'global.ysell.edit'"
            />

            <Modal type="danger">
              <template #trigger="{ setIsOpen }">
                <IconButton
                  @click="() => setIsOpen(true)"
                  color="gray"
                  variant="ghost"
                  :icon="TrashIcon"
                  v-can="'global.ysell.destroy'"
                />
              </template>

              <template #title>
                {{ $t("global", "Delete Ysell") }}
              </template>
              <template #content>
                {{
                  $t(
                    "global",
                    "Are you sure you want to delete selected Ysell? All data will be permanently removed from our servers forever. This action cannot be undone."
                  )
                }}
              </template>

              <template #buttons="{ setIsOpen }">
                <Button
                  @click.prevent="
                    () => {
                      action('delete', route('craftable-pro.ysells.destroy', item), {
                        onFinish: () => setIsOpen(false),
                      });
                    }
                  "
                  color="danger"
                  v-can="'global.ysell.destroy'"
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
import type { Ysell } from "./types";
import type { PageProps } from "craftable-pro/types/page";
import dayjs from "dayjs";



interface Props {
  ysells: PaginatedCollection<Ysell>;
}
defineProps<Props>();
const downloadFile = () => {
    const url = window.location.href.split("?");
    if(url.length > 1) {
      window.location = route('craftable-pro.ysells.export', url.pop()).slice(0, -1);
    } else {
      window.location = route('craftable-pro.ysells.export');
    }
}
</script>
