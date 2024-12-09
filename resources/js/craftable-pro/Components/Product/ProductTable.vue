<template>
    <Listing
        :baseUrl="props.baseUrl"
        :data="products"
        dataKey="products"
    >
        <template #actions>
            <div class="flex flex-wrap gap-1">
                <Tag
                    color="danger"
                    rounded
                    size="sm"
                >
                    {{ $t('global', 'Critical') }}
                </Tag>
                <Tag
                    color="warning"
                    rounded
                    size="sm"
                >
                    {{ $t('global', 'Shortage') }}
                </Tag>
                <Tag
                    color="success"
                    rounded
                    size="sm"
                >
                    {{ $t('global', 'Normal') }}
                </Tag>
                <Tag
                    color="info"
                    rounded
                    size="sm"
                >
                    {{ $t('global', 'Stocked') }}
                </Tag>
                <Tag
                    color="purple"
                    rounded
                    size="sm"
                >
                    {{ $t('global', 'Surplus') }}
                </Tag>
            </div>

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

            <!--        <ListingHeaderCell v-width-dragging sortBy="id">-->
            <!--            {{ $t("global", "Id") }}-->
            <!--        </ListingHeaderCell>-->
            <ListingHeaderCell v-width-dragging sortBy="ext_id">
                {{ $t("global", "Ext Id") }}
            </ListingHeaderCell>
            <ListingHeaderCell v-width-dragging sortBy="ean">
                {{ $t("global", "Ean") }}
            </ListingHeaderCell>
            <ListingHeaderCell v-width-dragging sortBy="product_type_id">
                {{ $t("global", "Product Type") }}
            </ListingHeaderCell>
            <ListingHeaderCell v-width-dragging>
                {{ $t("global", "Image") }}
            </ListingHeaderCell>
            <ListingHeaderCell v-width-dragging>
                {{ $t("global", "Title") }}
            </ListingHeaderCell>
            <!--          <ListingHeaderCell v-width-dragging>-->
            <!--              {{ $t("global", "Price") }}-->
            <!--          </ListingHeaderCell>-->
            <ListingHeaderCell v-width-dragging>
                {{ $t("global", "Stock") }}
            </ListingHeaderCell>
            <template v-if="income">
                <template v-for="warehouse in warehousesWithDates">
                    <template v-if="!warehouse?.virtual">
                        <ListingHeaderCell v-width-dragging v-for="(date, index) of warehouse.futureIncomesDates" class="max-w-[70px] min-w-[70px]">
                            {{ warehouse.name }}<br>
                            <span class="text-[9px]">{{ date }}</span>
                            <div class="flex gap-1">
                                <ModalDateMove :warehouse="warehouse" :date_from="date" />
                                <ModalDateAdd
                                    v-if="index === warehouse.futureIncomesDates.length - 1"
                                    :warehouse="warehouse"
                                    @submit="dateSubmit"/>
                            </div>
                        </ListingHeaderCell>
                    </template>
                    <ListingHeaderCell v-width-dragging v-else class="max-w-[70px]">
                        {{ warehouse.name }}
                    </ListingHeaderCell>
                </template>
            </template>
            <template v-else-if="forecast">
                <template v-for="warehouse in warehouses">
                    <ListingHeaderCell v-width-dragging v-if="!warehouse?.virtual" class="">
                        {{ warehouse.name }}
                    </ListingHeaderCell>
                </template>
            </template>
            <template v-else>
                <template v-for="warehouse in warehouses">
                    <ListingHeaderCell v-width-dragging v-if="!warehouse?.virtual" class="max-w-[70px]">
                        {{ warehouse.name }}
                    </ListingHeaderCell>
                </template>
            </template>

            <!--          <ListingHeaderCell v-width-dragging sortBy="additional_data">-->
            <!--              {{ $t("global", "Additional Data") }}-->
            <!--          </ListingHeaderCell>-->
            <ListingHeaderCell v-width-dragging>
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
            <template v-if="income">
                <template v-for="warehouse in warehousesWithDates">
                    <template v-if="!warehouse?.virtual">
                        <template v-for="date in warehouse.futureIncomesDates">
                        <ListingDataCell class="max-w-[70px]">
                            <div class="flex gap-2 items-center">
                                <TextInput
                                    v-can="'global.product.edit-income'"
                                    :model-value="getIncomeByDateAndWarehouse(item, warehouse, date)"
                                    name="income_quantity"
                                    class="income__input"
                                    :inputClass="stockClass(item, warehouse, getIncomeByWarehouse(item, warehouse, date))"
                                    @update:model-value="updateProductIncome(item, warehouse, date, $event)"
                                />
                                <span :class="stockClass(item, warehouse, getIncomeByWarehouse(item, warehouse, date))">
                                    {{ getIncomeByDateAndWarehouse(item, warehouse, date) }}
                                </span>
                            </div>
                        </ListingDataCell>
                        </template>
                    </template>
                    <ListingDataCell v-else class="max-w-[70px]">
                        <div class="flex gap-2 items-center">
                            {{ getPivotValue(item, warehouse, 'stock_quantity') }}
                        </div>
                    </ListingDataCell>
                </template>
            </template>
            <template v-else-if="forecast">
                <template v-for="warehouse in warehouses">
                    <ListingDataCell v-if="!warehouse?.virtual" class="">
                        <div class="flex gap-2 items-center relative tooltip">
                            <div :class="stockClass(item, warehouse, getIncomeByWarehouse(item, warehouse, date))">
                                {{ ((getConsumption(item, warehouse)  / filtersForm.days)  *
                                getMinMaxRange(warehouse, 'success').min) -
                                     (getPivotValue(item, warehouse, 'stock_quantity') + getIncomeByDateAndWarehouse(item, warehouse, date)) }}
                                /
                                {{ (((getConsumption(item, warehouse)  / filtersForm.days))  *
                                getMinMaxRange(warehouse, 'success').max) -
                            (getPivotValue(item, warehouse, 'stock_quantity') + getIncomeByDateAndWarehouse(item, warehouse, date)) }}

                            </div>
                            <span class="tooltip__text bg-gray-800 dark:bg-gray-100 text-gray-100 dark:text-gray-700">
                                {{ $t("global", "Income") }}: {{ getIncomeByDateAndWarehouse(item, warehouse, date) }} <br>
                                {{ $t("global", "Stock") }}: {{ getPivotValue(item, warehouse, 'stock_quantity') }} <br>
                                {{ $t("global", "Consumption") }}: {{ getConsumption(item, warehouse) }} / {{ (getConsumption(item, warehouse)  / filtersForm.days).toFixed(2) }} - per day <br>
                                {{ $t("global", "Min") }}: {{ getMinMaxRange(warehouse, 'success').min }} <br>
                                {{ $t("global", "Max") }}: {{ getMinMaxRange(warehouse, 'success').max }} <br>
                                ({{ $t("global", "Consumption") }} * {{ $t("global", "Min") }}/{{ $t("global", "Max") }}) - ({{ $t("global", "Stock") }} + {{ $t("global", "Income") }}) <br>
                            </span>
                        </div>
                    </ListingDataCell>
                </template>
            </template>
            <template v-else>
                <template v-for="warehouse in warehouses">
                    <ListingDataCell v-if="!warehouse?.virtual" class="max-w-[70px]">
                        <div class="flex gap-2 items-center">
                            <div :class="stockClass(item, warehouse)">
                                {{ getPivotValue(item, warehouse, 'stock_quantity') }}
                            </div>
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
    TextInput, Tag,
} from "craftable-pro/Components";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { Product } from "@/craftable-pro/Pages/Product/types";
import type { PageProps } from "craftable-pro/types/page";
import dayjs from "dayjs";
import { Warehouse } from "@/craftable-pro/Pages/Warehouse/types";
import { useAction } from "craftable-pro/hooks/useAction";
import debounce from "lodash/debounce";
import { useListingFilters } from "craftable-pro/hooks/useListingFilters";
import { computed, ref } from "vue";
import ModalDateAdd from "@/craftable-pro/Components/Product/ModalDateAdd.vue";
import ModalDateMove from "@/craftable-pro/Components/Product/ModalDateMove.vue";



interface Props {
    baseUrl: string;
    products: PaginatedCollection<Product>;
    warehouses: Object<string, Warehouse>;
    income?: boolean
    forecast?: boolean
}
const props = defineProps<Props>();

const { filtersForm, resetFilters, activeFiltersCount } = useListingFilters(
    props.baseUrl,
    {
        days: (usePage().props as PageProps)?.filter?.group ?? 6,
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

function dateSubmit(form) {
    const date = form.date
    mergeDates.value.push({
        warehouse_id: form.warehouse.id,
        income_date: `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`
    });
}

const mergeDates = ref([]);

const warehousesWithDates = computed(() => {
    return Object.values(props.warehouses).map(warehouse => {
        const futureIncomesDates = Array.from(new Set([
            ...warehouse.futureIncomesDates,
            ...mergeDates.value.filter(d => d.warehouse_id === warehouse.id).map(d => d.income_date)
        ]))
            .sort((a, b) => new Date(a) - new Date(b));

        return {
            ...warehouse,
            futureIncomesDates
        }
    });
});

const { action } = useAction();

const updateProductIncome = debounce((product: Product, warehouse: Warehouse, date: string, value: string) => {
    action('patch',
        route('craftable-pro.products.update-income', {product: product.id, warehouse: warehouse.id }),
        { income_quantity: value, income_date: date }
    )
}, 1000);

const getPivotValue = (product: Product, warehouse: Warehouse, key) => {
    const wh = product?.warehouses?.find(wh => wh.id === warehouse.id);
    return wh?.pivot?.[key] ?? 0; // Возвращаем 0 по умолчанию, если данных нет
};

const getIncomeByDateAndWarehouse = (product: Product, warehouse: Warehouse, date: string) => {
    return product.incomes?.find(income => income.income_date === date && income.warehouse_id === warehouse.id)?.quantity ?? 0; // Возвращаем 0 по умолчанию, если данных нет
};

const getIncomeByWarehouse = (product: Product, warehouse: Warehouse) => {
    return product.incomes?.filter(income => income.warehouse_id === warehouse.id).reduce((acc, income) => acc + income.quantity, 0) ?? 0; // Возвращаем 0 по умолчанию, если данных нет
};

const getConsumption = (product: Product, warehouse: Warehouse) => {
    return product?.stock_changes?.total_consumption?.[warehouse.ysell_name] ?? 0;
};

const getMinMaxRange = (warehouse: Warehouse, color) => {
    return warehouse?.settings?.ranges.find(r => r.color === color) ?? {};
};

function stockClass(product: Product, warehouse: Warehouse, income: number = 0) {
    let consumption = product?.stock_changes?.total_consumption?.[warehouse.ysell_name] ?? 0;
    const stock = getPivotValue(product, warehouse, 'stock_quantity');
    // const income = includeIncome ? product?.warehouses?.find(wh => wh.id === warehouse.id)?.pivot?.income_quantity : 0;
    if (consumption !== undefined && stock !== undefined) {
        consumption = (stock + (income ?? 0)) / (consumption / filtersForm.days)
        for (const range of warehouse?.settings?.ranges) {
            if (consumption >= range.from && consumption <= range.to) {
                return range.color;
            }
            if ((range?.min === undefined || consumption >= range.min) &&
                (range?.max === undefined || consumption <= range.max)) {
                return `!text-${range.color}-500`;
            }
        }
    }

    return '';
}

function getNextDay(dateString) {
    const date = new Date(dateString);
    date.setDate(date.getDate() + 1); // Додаємо один день

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Місяці в JavaScript починаються з 0
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

</script>

<style lang="scss" scoped>
.income__input :deep(input) {
    background: transparent!important;
    border:none!important;
    box-shadow: none!important;
}
.income__input + * {
    display: none;
}

.tooltip {
    &:hover .tooltip__text {
        visibility: visible;
    }
    &__text {
        visibility: hidden;
        width: 100%;
        min-width: 220px;
        text-align: center;
        padding: 5px 0;
        border-radius: 6px;

        /* Position the tooltip text - see examples below! */
        position: absolute;
        z-index: 420;
        bottom: 50%;
        transform: translate(0px, 50%);
        left: 100%;
        &::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: black transparent transparent transparent;
        }
    }
}
</style>
