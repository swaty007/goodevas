<template>
    <Listing
        :baseUrl="props.baseUrl"
        :data="products"
        dataKey="products"
    >
        <template #actions>
            <FiltersDropdown
                :activeFiltersCount="activeFiltersCount"
                :resetFilters="resetFilters"
            >
                <Multiselect
                    v-model="filtersForm.days"
                    :options="[
                        6,
                        30,
                        90,
                        120,
                    ]"
                    :label="$t('global', 'Days')"
                    mode="single"
                    name="days"
                />
            </FiltersDropdown>
        </template>
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

            <!--        <ListingHeaderCell sortBy="id">-->
            <!--            {{ $t("global", "Id") }}-->
            <!--        </ListingHeaderCell>-->
            <ListingHeaderCell sortBy="ext_id">
                {{ $t("global", "Ext Id") }}
            </ListingHeaderCell>
            <ListingHeaderCell sortBy="ean">
                {{ $t("global", "Ean") }}
            </ListingHeaderCell>
            <ListingHeaderCell sortBy="product_type_id">
                {{ $t("global", "Product Type Id") }}
            </ListingHeaderCell>
            <ListingHeaderCell>
                {{ $t("global", "Image") }}
            </ListingHeaderCell>
            <ListingHeaderCell>
                {{ $t("global", "Title") }}
            </ListingHeaderCell>
            <!--          <ListingHeaderCell>-->
            <!--              {{ $t("global", "Price") }}-->
            <!--          </ListingHeaderCell>-->
            <ListingHeaderCell>
                {{ $t("global", "Stock") }}
            </ListingHeaderCell>
            <template v-for="warehouse in warehouses">
                <ListingHeaderCell v-if="!warehouse?.virtual || income" class="max-w-[70px]">
                    {{ warehouse.name }}
                    <template v-if="income">
                        / {{ $t("global", "Income") }}
                    </template>
                </ListingHeaderCell>
            </template>
            <!--          <ListingHeaderCell sortBy="additional_data">-->
            <!--              {{ $t("global", "Additional Data") }}-->
            <!--          </ListingHeaderCell>-->
            <ListingHeaderCell>
                <span class="sr-only">{{ $t("global", "Actions") }}</span>
            </ListingHeaderCell>
        </template>
        <template #tableRow="{ item, action }: any">

            <!--        <ListingDataCell>-->
            <!--             {{ item.id }}-->
            <!--        </ListingDataCell>-->
            <ListingDataCell>
                {{ item.ext_id }}
            </ListingDataCell>
            <ListingDataCell>
                {{ item.ean }}
            </ListingDataCell>

            <ListingDataCell>
                {{ item?.type?.name }}
            </ListingDataCell>
            <ListingDataCell>
                <Avatar
                    :src="item?.additional_data?.image"
                    name="Logo"
                />
            </ListingDataCell>
            <ListingDataCell>
                {{ item?.additional_data?.title }}
            </ListingDataCell>
            <!--          <ListingDataCell>-->
            <!--              {{ item?.additional_data?.purchase_price }}-->
            <!--          </ListingDataCell>-->
            <ListingDataCell>
                {{ item?.additional_data?.netto }}
            </ListingDataCell>
            <template v-for="warehouse in warehouses">
                <template v-if="!warehouse?.virtual || income">
                    <ListingDataCell class="max-w-[70px]">
                        <div class="flex gap-2 items-center">
                            <template v-if="income">
                                <template v-if="!warehouse?.virtual && income">
                                    <TextInput
                                        v-can="'global.product.edit-income'"
                                        :model-value="getPivotValue(item, warehouse, 'income_quantity')"
                                        name="income_quantity"
                                        class="income__input"
                                        @update:model-value="updateProductIncome(item, warehouse, $event)"
                                    />
                                    <span>
                                        {{ getPivotValue (item, warehouse, 'income_quantity') }}
                                    </span>
                                </template>
                                <strong v-else>
                                    {{ getPivotValue(item, warehouse, 'stock_quantity') }}
                                </strong>
                            </template>
                            <strong v-else>
                                <div :class="stockClass(item, warehouse)">
                                    {{ getPivotValue(item, warehouse, 'stock_quantity') }}
                                </div>
                            </strong>
                        </div>
                    </ListingDataCell>
                </template>
            </template>
            <!--          <ListingDataCell>-->
            <!--             <pre>-->
            <!--                 {{ item.additional_data }}-->
            <!--             </pre>-->
            <!--          </ListingDataCell>-->
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
    TextInput,
} from "craftable-pro/Components";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { Product } from "@/craftable-pro/Pages/Product/types";
import type { PageProps } from "craftable-pro/types/page";
import dayjs from "dayjs";
import { Warehouse } from "@/craftable-pro/Pages/Warehouse/types";
import { useAction } from "craftable-pro/hooks/useAction";
import debounce from "lodash/debounce";
import { useListingFilters } from "craftable-pro/hooks/useListingFilters";



interface Props {
    baseUrl: string;
    products: PaginatedCollection<Product>;
    warehouses: Warehouse[]
    income: boolean
}
const props = defineProps<Props>();

const { filtersForm, resetFilters, activeFiltersCount } = useListingFilters(
    props.baseUrl,
    {
        days: (usePage().props as PageProps)?.filter?.group ?? 7,
    }
);
const downloadFile = () => {
    const url = window.location.href.split("?");
    if(url.length > 1) {
        window.location = route('craftable-pro.products.export', url.pop()).slice(0, -1);
    } else {
        window.location = route('craftable-pro.products.export');
    }
}

const { action } = useAction();

const updateProductIncome = debounce((product: Product, warehouse: Warehouse, value: string) => {
    action('patch',
        route('craftable-pro.products.update-income', {product: product.id, warehouse: warehouse.id }),
        { income_quantity: value }
    )
}, 1000);

const getPivotValue = (product: Product, warehouse: Warehouse, key) => {
    const wh = product?.warehouses?.find(wh => wh.id === warehouse.id);
    return wh?.pivot?.[key] ?? 0; // Возвращаем 0 по умолчанию, если данных нет
};

function stockClass(product: Product, warehouse: Warehouse) {
    let consumption = product?.stock_changes?.total_consumption?.[warehouse.ysell_name]
    const stock = product?.warehouses?.find(wh => wh.id === warehouse.id)?.pivot?.stock_quantity
    if (consumption !== undefined && stock !== undefined) {
        consumption = stock / (consumption / filtersForm.days)
        if (consumption < 45) {
            return 'text-red-500'; // Deficit (< 45 days)
        }
        if (consumption >= 45 && consumption < 90) {
            return 'text-yellow-500'; // Sufficient stock (45-90 days)
        }
        if (consumption >= 90 && consumption <= 120) {
            return 'text-green-500'; // Ideal stock (90-120 days)
        }
        if (consumption > 120 && consumption <= 180) {
            return 'text-blue-500'; // Overstock (120-180 days)
        }
        if (consumption > 180) {
            return 'text-purple-500'; // Super overstock (> 180 days)
        }
    }

    return '';
}

</script>

<style lang="scss" scoped>
.income__input + * {
    display: none;
}
</style>
