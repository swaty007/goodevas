<template>
    <Card
        no-padding
        :class="[$attrs.class, 'global__table']"
    >
        <template #header>
            <CardHeader
                class="border border-transparent"
                :class="{
                    'border-2 border-b-2 border-dashed !border-primary-400 bg-primary-50 dark:bg-primary-800':
                        indeterminate || allItemsSelected,
                }"
            >
                <div
                    v-auto-animate="{ duration: 150 }"
                    class="relative flex items-center"
                    :class="{ '-m-px': indeterminate || allItemsSelected }"
                >
                    <div class="h-[34px]" />
                    <Checkbox
                        v-if="withBulkSelect"
                        class="-ml-px"
                        :checked="indeterminate || allItemsSelected"
                        :indeterminate="indeterminate"
                        @change="
                            $event.target.checked
                                ? (selectedItems = collection.map((p) => p.id))
                                : unselectAllItems()
                        "
                    />
                    <div
                        v-if="selectedItems?.length > 0"
                        v-auto-animate
                        class="flex items-center gap-x-4 pl-6 text-sm text-gray-700 dark:text-gray-200"
                    >
                        <div class="flex-shrink-0">
                            <span class="leading-8 sm:whitespace-nowrap">
                                {{
                                    $tChoice(
                                        "craftable-pro",
                                        "{1} :count item selected.|[2,*] :count items selected.",
                                        selectedItems.length
                                    )
                                }}
                            </span>
              &nbsp;
                            <span v-if="!allItemsSelected">
                                <Button
                                    :disabled="loadingAllItems"
                                    size="sm"
                                    class="whitespace-nowrap"
                                    variant="link"
                                    @click="selectAllItems"
                                >
                                    {{ $t("craftable-pro", "Select all items.") }}
                                </Button>
                            </span>
                        </div>

                        <slot
                            name="bulkActions"
                            :base-url="baseUrl"
                            :selected-items="selectedItems"
                            :bulk-action="bulkAction"
                            :bulk-action-form="bulkActionForm"
                        >
                            <Button
                                color="gray"
                                variant="outline"
                                size="sm"
                                :left-icon="TrashIcon"
                                @click="() => bulkAction('delete', `${baseUrl}/bulk-delete`)"
                            >
                                {{ $t("craftable-pro", "Delete") }}
                            </Button>
                        </slot>
                    </div>
                    <div
                        v-else
                        class="flex w-full items-center justify-between gap-3"
                        :class="{ 'pl-6': withBulkSelect }"
                    >
                        <slot
                            name="header"
                            :search-form="searchForm"
                            :reset-search="resetSearch"
                        >
                            <div class="w-2/6">
                                <TextInput
                                    v-model="searchForm.search"
                                    name="search"
                                    size="sm"
                                    :left-icon="MagnifyingGlassIcon"
                                    :placeholder="$t('craftable-pro', 'Search')"
                                    :clearable="true"
                                    class="w-full"
                                />
                            </div>
                            <slot name="actions" />
                        </slot>
                    </div>
                </div>
            </CardHeader>
        </template>

        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="relative overflow-hidden md:overflow-visible">
                    <EmptyListing v-if="!collection?.length" />
                    <table
                        v-else
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-600"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <ListingHeaderCell v-width-dragging
                                    v-if="withBulkSelect"
                                    class="w-8 sm:w-14"
                                />
                                <slot name="tableHead">
                                    <slot
                                        v-for="(column, index) in columns"
                                        :key="column"
                                        :name="`tableHeading-${column}`"
                                        :item="item"
                                        :column="column"
                                        :index="index"
                                    >
                                        <ListingHeaderCell
                                            v-width-dragging
                                            :sort-by="column"
                                        >
                                            <!-- TODO: get translation for col -->
                                            {{ compColumnValue(column) }}
                                        </ListingHeaderCell>
                                    </slot>
                                    <ListingHeaderCell v-width-dragging v-if="editResource">
                                        <span class="sr-only">{{
                                            $t("craftable-pro", "Edit")
                                        }}</span>
                                    </ListingHeaderCell>
                                </slot>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-700">
                            <tr
                                v-for="item in collection"
                                :key="item.id"
                                :class="{
                                    'bg-gray-200 dark:bg-gray-600': selectedItems.includes(item.id),
                                }"
                                @click="toggleSelectedItem(item.id)"
                            >
                                <ListingDataCell
                                    v-if="withBulkSelect"
                                    v-auto-animate="{ duration: 150 }"
                                    class="relative w-8 sm:w-14"
                                >
                                    <div
                                        v-if="selectedItems.includes(item.id)"
                                        class="absolute inset-y-0 left-0 w-0.5 bg-primary-600"
                                    />
                                    <Checkbox
                                        v-model="selectedItems"
                                        :input-value="item.id"
                                    />
                                </ListingDataCell>
                                <slot
                                    name="tableRow"
                                    :item="item"
                                    :action="action"
                                    :unselect-all-items="unselectAllItems"
                                >
                                    <slot
                                        v-for="column in columns"
                                        :key="column"
                                        :name="`tableColumn-${column}`"
                                        :item="item"
                                        :action="action"
                                        :unselect-all-items="unselectAllItems"
                                    >
                                        <!--v-bind="column"-->
                                        <ListingDataCell>
                                            {{ compColumnValue(item[column]) }}
                                        </ListingDataCell>
                                    </slot>
                                    <ListingDataCell v-if="editResource">
                                        <Link
                                            :href="item.resource_url"
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100"
                                        >
                                            <ChevronRightIcon class="ml-auto h-5 w-5" />
                                        </Link>
                                    </ListingDataCell>
                                </slot>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <template #footer>
            <CardFooter v-if="withPagination">
                <Pagination :pagination="pagination" />
            </CardFooter>
        </template>
    </Card>
</template>
<script setup lang="ts">
import { computed, defineEmits, provide, useSlots, watch } from "vue";
import { ChevronRightIcon, MagnifyingGlassIcon, } from "@heroicons/vue/24/outline";
import { TrashIcon } from "@heroicons/vue/24/solid";
import { Button, Card, CardFooter, CardHeader, Checkbox, Pagination, TextInput, } from "craftable-pro/Components";
import { EmptyListing, ListingDataCell, ListingHeaderCell } from "craftable-pro/Components/Listing/index";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import { Model } from "craftable-pro/types/models";
import { useBulkSelect } from "craftable-pro/hooks/useBulkSelect";
import { useBulkAction } from "craftable-pro/hooks/useBulkAction";
import { useAction } from "craftable-pro/hooks/useAction";
import { useListingSearch } from "craftable-pro/hooks/useListingSearch";
import { route } from "ziggy-js";

interface Props {
    data: PaginatedCollection<Model>;
    dataKey?: string;
    baseUrl?: string;
    columns?: string[];
    withBulkSelect?: boolean;
    filters?: object;
    withPagination?: boolean;
    editResource?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    withBulkSelect: true,
    baseUrl: route(route().current(), route().params),
    dataKey: "data",
    withPagination: true,
    editResource: false,
});

provide("listingBaseUrl", props.baseUrl);

if (props.dataKey) {
    provide("listingDataKey", props.dataKey);
}

const slots = useSlots();

const emit = defineEmits(["update:selectedItems"]);

const collection = computed(() => props.data.data);

const pagination = computed(() => props.data);

const {
    selectedItems,
    indeterminate,
    allItemsSelected,
    selectAllItems,
    loadingAllItems,
    unselectAllItems,
} = useBulkSelect();

const { bulkAction, bulkActionForm } = useBulkAction(selectedItems);

const { action } = useAction();

const { searchForm, resetSearch } = useListingSearch(props.baseUrl);

const toggleSelectedItem = (id: number) => {
    if (selectedItems && selectedItems.value.length) {
        if (selectedItems.value.includes(id)) {
            selectedItems.value = selectedItems.value.filter(i => i !== id)
        } else {
            selectedItems.value.push(id)
        }
    }
}

const compColumnValue = (col: string | object) => {
    if (col instanceof Object) return col.value
    return col
}

watch(selectedItems, (val: number[]) => {
    bulkActionForm.ids = val
    emit('update:selectedItems', val)
}, { deep: true })
</script>
