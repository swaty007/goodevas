<template>
  <PageContent>
    <div class="mx-auto max-w-3xl 2xl:max-w-4xl">


      <Card>
        <div class="space-y-4">
            <TextInput
                v-model="form.name"
                name="name"
                :label="$t('global', 'Name')"

            />

            <TextInput
                v-model="form.country_id"
                name="country_id"
                :label="$t('global', 'Country Id')"

            />

            <Checkbox
                v-model="form.virtual"
                name="virtual"
                :label="$t('global', 'Virtual')"

            />


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
import type { WarehouseForm } from "./types";



interface Props {
  form: InertiaForm<WarehouseForm>;
  submit: void;

}

const props = defineProps<Props>();
</script>
