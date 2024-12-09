<template>
  <PageHeader :title="$t('global', '[[modelNamePlural]]')">
    <Button
      :leftIcon="PlusIcon"
      :as="Link"
      :href="route('[[modelCreateRoute]]')"
      v-can="'global.[[modelPermissionName]].create'"
    >
      {{ $t("global", "New [[modelName]]") }}
    </Button>
    [[exportButton]]
  </PageHeader>

  <PageContent>
    <Listing
      :baseUrl="route('[[modelIndexRoute]]')"
      :data="[[modelNamePluralLowerCase]]"
      dataKey="[[modelNamePluralLowerCase]]"
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
              v-can="'global.[[modelPermissionName]].destroy'"
            >
              {{ $t("global", "Delete") }}
            </Button>
          </template>

          <template #title>
            {{ $t("global", "Delete [[modelName]]") }}
          </template>
          <template #content>
            {{
              $t(
                "global",
                "Are you sure you want to delete selected [[modelName]]? All data will be permanently removed from our servers forever. This action cannot be undone."
              )
            }}
          </template>

          <template #buttons="{ setIsOpen }">
            <Button
              @click.prevent="
                () => {
                  bulkAction('post', route('[[modelBulkDestroyRoute]]'), {
                    onFinish: () => setIsOpen(false),
                  });
                }
              "
              color="danger"
              v-can="'global.[[modelPermissionName]].destroy'"
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
        [[listingHeaderCell]]
        <ListingHeaderCell v-width-dragging>
          <span class="sr-only">{{ $t("global", "Actions") }}</span>
        </ListingHeaderCell>
      </template>
      <template #tableRow="{ item, action }: any">
        [[listingDataCell]]
        <ListingDataCell>
          <div class="flex items-center justify-end gap-3">
            <IconButton
              :as="Link"
              :href="route('[[modelEditRoute]]', item)"
              variant="ghost"
              color="gray"
              :icon="PencilSquareIcon"
              v-can="'global.[[modelPermissionName]].edit'"
            />

            <Modal type="danger">
              <template #trigger="{ setIsOpen }">
                <IconButton
                  @click="() => setIsOpen(true)"
                  color="gray"
                  variant="ghost"
                  :icon="TrashIcon"
                  v-can="'global.[[modelPermissionName]].destroy'"
                />
              </template>

              <template #title>
                {{ $t("global", "Delete [[modelName]]") }}
              </template>
              <template #content>
                {{
                  $t(
                    "global",
                    "Are you sure you want to delete selected [[modelName]]? All data will be permanently removed from our servers forever. This action cannot be undone."
                  )
                }}
              </template>

              <template #buttons="{ setIsOpen }">
                <Button
                  @click.prevent="
                    () => {
                      action('delete', route('[[modelDestroyRoute]]', item), {
                        onFinish: () => setIsOpen(false),
                      });
                    }
                  "
                  color="danger"
                  v-can="'global.[[modelPermissionName]].destroy'"
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
import type { [[modelIndexName]] } from "./types";
import type { PageProps } from "craftable-pro/types/page";
import dayjs from "dayjs";

[[translatableFunctionality]]

interface Props {
  [[modelNamePluralLowerCase]]: PaginatedCollection<[[modelIndexName]]>;
}
defineProps<Props>();
[[exportFunctionality]]
</script>
