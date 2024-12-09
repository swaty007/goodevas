<template>
    <Modal type="warning" size="md">
        <template #title>
            {{ $t('global', 'Date Move') }}
        </template>
        <template #trigger="{ setIsOpen }">
            <IconButton
                @click="() => setIsOpen(true)"
                size="xs"
                color="gray"
                variant="outline"
                :icon="PaperClipIcon"
            />
        </template>
        <template #content>
            <p>
                {{ $t('global', 'Move date from') }}
                <strong>{{ props.date_from }}</strong> {{ $t('global', 'to') }}
            </p>
            <DatePicker
                v-model="form.income_date"
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
                        submit();
                        emit('submit', form);
                    }
                "
            >
                {{ $t('global', 'Move') }}
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
import { ArrowDownTrayIcon, PaperClipIcon } from '@heroicons/vue/24/outline';
import {
    Button,
    DatePicker,
    IconButton,
    Modal,
} from 'craftable-pro/Components';
import { useAction } from 'craftable-pro/hooks/useAction';
import { defineEmits, ref } from 'vue';

interface Props {
    warehouse: Warehouse;
    date_from: string;
}

const props = defineProps<Props>();

const emit = defineEmits(['submit']);

const { action } = useAction();

const form = ref({
    income_date: new Date(),
    warehouse: props.warehouse,
});

const submit = () => {
    const date = form.value.income_date;
    const income_date = `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
    action(
        'post',
        route('craftable-pro.products.move-income', {
            warehouse: props.warehouse.id,
        }),
        {
            date_from: props.date_from,
            income_date: income_date,
        },
    );
};
</script>
