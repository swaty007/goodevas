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

            <TextInput
                v-model="form.ysell_name"
                name="ysell_name"
                :label="$t('global', 'Ysell Name')"
                :disabled="true"
            />

            <div v-for="setting in form.settings.ranges" class="flex gap-2 w-full">
                <TextInput
                    v-model.number="setting.min"
                    name="min"
                    class="w-full"
                    :label="$t('global', 'Min')"
                />
                <TextInput
                    v-model.number="setting.max"
                    name="max"
                    class="w-full"
                    :label="$t('global', 'Max')"
                />
                <Multiselect
                    v-model="setting.color"
                    name="color"
                    class="w-full"
                    :options="['success',
                                   'info',
                                   'warning',
                                   'danger',
                                   'primary',
                                   'secondary',
                                   'amber',
                                   'purple',
                                   'cyan',
                                   'gray',]"
                    mode="single"
                    :label="$t('global', 'Color')"
                >
                    <template #singlelabel="{ value }">
                        <Tag
                            :color="value.value"
                            rounded
                            size="sm"
                        >
                            {{ value.value }}
                        </Tag>
                    </template>
                    <template #option="{ option, search }">
                        <Tag
                            :color="option.value"
                            rounded
                            size="sm"
                        >
                            {{ option.value }}
                        </Tag>
                    </template>
                </Multiselect>
            </div>


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
import { WarehouseForm, WarehouseSettings } from "./types";
import { Tag } from "craftable-pro/Components";



interface Props {
  form: InertiaForm<WarehouseForm>;
  submit: void;
    defaultSettings: WarehouseSettings
}

const props = defineProps<Props>();
</script>
