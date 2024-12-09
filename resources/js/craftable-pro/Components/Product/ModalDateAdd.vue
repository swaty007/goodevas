<template>
    <Modal type="success" size="md">
        <template #title>
            {{ $t('global', 'Add Date') }}
        </template>
        <template #trigger="{ setIsOpen }">
            <IconButton
                @click="() => setIsOpen(true)"
                size="xs"
                :icon="PlusIcon"
            />
        </template>
        <template #content>
            <DatePicker
                v-model="form.date"
                name="date"
                :label="$t('global', 'Date')"
                format="date"
                :placeholder="$t('global', 'Select date')"
                class="w-full"
            />
        </template>

        <template #buttons="{ setIsOpen }">
            <Button
                :left-icon="ArrowDownTrayIcon"
                @click="
                    () => {
                        setIsOpen();
                        emit('submit', form);
                    }
                "
            >
                {{ $t('global', 'Add') }}
            </Button>
            <Button
                color="gray"
                variant="outline"
                @click.prevent="() => setIsOpen()"
            >
                {{ $t('global', 'Cancel') }}
            </Button>
        </template>
    </Modal>
</template>

<script setup lang="ts">
import { Warehouse } from '@/craftable-pro/Pages/Warehouse/types';
import { ArrowDownTrayIcon, PlusIcon } from '@heroicons/vue/24/outline';
import {
    Button,
    DatePicker,
    IconButton,
    Modal,
} from 'craftable-pro/Components';
import { defineEmits, ref } from 'vue';

interface Props {
    warehouse: Warehouse;
}

const props = defineProps<Props>();

const emit = defineEmits(['submit', 'delete']);

const form = ref({
    date: new Date(),
    warehouse: props.warehouse,
});
</script>
