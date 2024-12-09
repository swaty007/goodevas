<template>
    <Modal type="success" class="inline-block">
        <template #trigger="{ setIsOpen }">
            <Button
                :leftIcon="ArrowUpTrayIcon"
                class="ml-2"
                @click="() => setIsOpen(true)"
                v-can="'global.product.edit-income'"
            >
                <slot>
                    {{ $t('global', 'Import') }}
                </slot>
            </Button>
        </template>

        <template #title>
            {{ $t('global', 'Import Users by csv, xls, xlsx file') }}
        </template>
        <template #content>
            <div class="mb-5">
                <Button
                    :leftIcon="ArrowDownTrayIcon"
                    :as="Link"
                    size="xs"
                    class="mb-2"
                    @click="downloadFile()"
                >
                    {{ $t('global', 'Download Example') }} CSV
                </Button>
                <Button
                    :leftIcon="ArrowDownTrayIcon"
                    :as="Link"
                    size="xs"
                    class="mb-2 ml-2"
                    @click="downloadFile('xls')"
                >
                    {{ $t('global', 'Download Example') }} XLS
                </Button>
                <Button
                    :leftIcon="ArrowDownTrayIcon"
                    :as="Link"
                    size="xs"
                    class=""
                    @click="downloadFile('xlsx')"
                >
                    {{ $t('global', 'Download Example') }} XLSX
                </Button>
            </div>
            <input
                class="form-control"
                type="file"
                multiple="false"
                @input="switchFile($event)"
            />
        </template>

        <template #buttons="{ setIsOpen }">
            <Button
                @click.prevent="submitImport(setIsOpen)"
                color="success"
                v-can="'global.product.edit-income'"
            >
                {{ $t('global', 'Confirm') }}
            </Button>
            <Button
                @click.prevent="() => setIsOpen(false)"
                color="danger"
                variant="outline"
            >
                {{ $t('global', 'Cancel') }}
            </Button>
        </template>
    </Modal>
</template>
<script setup lang="ts">
import { useToast } from '@brackets/vue-toastification';
import { ArrowDownTrayIcon, ArrowUpTrayIcon } from '@heroicons/vue/24/outline';
import { Link, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { Button, Modal } from 'craftable-pro/Components';
import { useAction } from 'craftable-pro/hooks/useAction';
import { trans } from 'craftable-pro/plugins/laravel-vue-i18n';
import { route } from 'ziggy-js';

const { action } = useAction();
const downloadFile = (format = 'csv') => {
    window.location = route('craftable-pro.products.export-income', { format });
};

const toast = useToast();

const form = useForm({
    // Name of media collection
    file: [],
});

const switchFile = (e: HTMLInputElement) => {
    form.file = e.target.files[0];
};
const submitImport = (cb) => {
    axios
        .post(route('craftable-pro.products.import-income'), form.data(), {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
        .then((response: any) => {
            form.file = [];
            cb(false);
            toast.success(trans('craftable-pro', 'Import started'));
            router.reload({ only: ['data', 'flash'] });
        })
        .catch((error) => {
            toast.error(error.response.data.message);
            console.error(error);
        });
};
</script>
