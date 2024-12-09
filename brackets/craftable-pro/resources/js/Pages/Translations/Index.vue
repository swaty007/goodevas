<template>
  <PageHeader :title="$t('craftable-pro', 'Translations')">
    <div class="flex">
      <ExportModal
        @toggle-open="exportModalOpened = !exportModalOpened"
        :open="exportModalOpened"
        :locales="locales"
        v-can="'craftable-pro.translation.export'"
      />
      <ImportModal
        @toggle-open="importModalOpened = !importModalOpened"
        :open="importModalOpened"
        :locales="locales"
        v-can="'craftable-pro.translation.import'"
      />
      <ButtonGroup>
        <Button
          @click="
            () => {
              action('post', `/admin/translations/publish`);
              reload();
            }
          "
          v-can="'craftable-pro.translation.publish'"
        >
          {{ $t("craftable-pro", "Publish translations") }}
        </Button>
        <Dropdown
          noContentPadding
          v-can:any="[
            'craftable-pro.translation.export',
            'craftable-pro.translation.import',
            'craftable-pro.translation.rescan',
          ]"
        >
          <template #button>
            <IconButton :icon="ChevronDownIcon" class="rounded-l-none" />
          </template>

          <template #content>
            <div class="py-1">
              <DropdownItem
                @click="
                  () => {
                    action('post', `/admin/translations/rescan`);
                    toast.warning(
                      $t('craftable-pro', 'Scanning translations...')
                    );
                  }
                "
                v-can="'craftable-pro.translation.rescan'"
              >
                {{ $t("craftable-pro", "Re-scan translations") }}
              </DropdownItem>
              <DropdownItem
                @click="exportModalOpened = true"
                v-can="'craftable-pro.translation.export'"
              >
                {{ $t("craftable-pro", "Export") }}
              </DropdownItem>
              <DropdownItem
                @click="importModalOpened = true"
                v-can="'craftable-pro.translation.import'"
              >
                {{ $t("craftable-pro", "Import") }}
              </DropdownItem>
            </div>
          </template>
        </Dropdown>
      </ButtonGroup>
    </div>
  </PageHeader>

  <PageContent>
    <Listing
      :data="data"
      :baseUrl="route('craftable-pro.translations.index')"
      :withBulkSelect="false"
    >
      <template #actions>
        <FiltersDropdown
          :activeFiltersCount="activeFiltersCount"
          :resetFilters="resetFilters"
        >
          <Multiselect
            v-model="filtersForm.group"
            :options="groups"
            :label="$t('craftable-pro', 'Groups')"
            mode="tags"
            name="groups"
          />
        </FiltersDropdown>
      </template>
      <template #tableHead>
        <ListingHeaderCell v-width-dragging sortBy="group">
          {{ $t("craftable-pro", "Group") }}
        </ListingHeaderCell>

        <ListingHeaderCell v-width-dragging sortBy="key">
          {{ $t("craftable-pro", "Default") }}
        </ListingHeaderCell>

        <ListingHeaderCell v-width-dragging>
          {{ ($page.props as PageProps).auth.user.locale }}
        </ListingHeaderCell>

        <ListingHeaderCell v-width-dragging>
          {{ $t("craftable-pro", "Last update") }}
        </ListingHeaderCell>

        <ListingHeaderCell v-width-dragging></ListingHeaderCell>
      </template>
      <template #tableRow="{ item, action }">
        <ListingDataCell>
          {{ item.group }}
        </ListingDataCell>

        <ListingDataCell>
          <div class="max-w-sm overflow-hidden text-ellipsis">
            {{ item.key }}
          </div>
        </ListingDataCell>

        <ListingDataCell>
          <div class="max-w-sm overflow-hidden text-ellipsis">
            {{ item.text[($page.props as PageProps).auth.user.locale] }}
          </div>
        </ListingDataCell>

        <ListingDataCell>
          {{ dayjs(item.updated_at).format("DD.MM.YYYY HH:mm") }}
        </ListingDataCell>

        <ListingDataCell>
          <div class="flex items-center justify-end gap-3">
            <EditTranslationModal
              :language-line="item"
              :locales="locales"
              v-can="'craftable-pro.translation.edit'"
            ></EditTranslationModal>
          </div>
        </ListingDataCell>
      </template>
    </Listing>
  </PageContent>
</template>

<script lang="ts" setup>
import { EllipsisVerticalIcon } from "@heroicons/vue/24/solid";
import {
  Button,
  Listing,
  ListingDataCell,
  ListingHeaderCell,
  Multiselect,
  PageHeader,
  PageContent,
  IconButton,
  Dropdown,
  FiltersDropdown,
  DropdownItem,
  ButtonGroup,
} from "craftable-pro/Components";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { LanguageLine } from "craftable-pro/types/models";
import type { PageProps } from "craftable-pro/types/page";
import { useAction } from "craftable-pro/hooks/useAction";
import EditTranslationModal from "craftable-pro/Pages/Translations/Components/EditTranslationModal.vue";
import ExportModal from "craftable-pro/Pages/Translations/Components/ExportModal.vue";
import ImportModal from "craftable-pro/Pages/Translations/Components/ImportModal.vue";
import { useToast } from "@brackets/vue-toastification";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useListingFilters } from "craftable-pro/hooks/useListingFilters";
import dayjs from "dayjs";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";

interface Props {
  data: PaginatedCollection<LanguageLine>;
  groups: string[];
  locales: string[];
}

const toast = useToast();
const { action } = useAction();
const exportModalOpened = ref<boolean>(false);
const importModalOpened = ref<boolean>(false);

defineProps<Props>();

const { filtersForm, resetFilters, activeFiltersCount } = useListingFilters(
  route("craftable-pro.translations.index"),
  {
    group: (usePage().props as PageProps)?.filter?.group ?? null,
  }
);

const reload = () => {
  window.location.reload();
};
</script>
