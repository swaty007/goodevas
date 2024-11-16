<template>
  <PageContent>
    <div class="mx-auto max-w-3xl 2xl:max-w-4xl">


      <Card>
        <div class="space-y-4">
            <TextInput
                v-model="form.ext_id"
                name="ext_id"
                :label="$t('global', 'Ext Id')"

            />

            <TextInput
                v-model="form.ean"
                name="ean"
                :label="$t('global', 'Ean')"

            />

            <Multiselect
                v-model="form.product_type_id"
                name="product_type_id"
                :label="$t('global', 'Product Type')"
                :options="productTypes"
                mode="single"
            />

            <label for="additional_data" class="flex items-center justify-between gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                {{ $t('global', 'Additional Data') }}
            </label>
            <pre>
                {{ form.additional_data }}
            </pre>

        </div>
      </Card>
    </div>
  </PageContent>
</template>

<script setup lang="ts">
import {
    Card,
    TextInput,
    Wysiwyg,
    TextArea,
    PageContent,
    DatePicker,
    Checkbox,
    Dropzone,
    ImageUpload,
    Multiselect,
    CardLocaleSwitcher
} from "craftable-pro/Components";
import { InertiaForm } from "craftable-pro/types";
import type { ProductForm } from "./types";



interface Props {
  form: InertiaForm<ProductForm>;
    productTypes: Array<{ value: string | number, label: string }>
  submit: void;

}

const props = defineProps<Props>();
</script>
