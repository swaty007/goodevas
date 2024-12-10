<template>
    <PageContent class="min-h-screen">
        <div class="mx-auto max-w-3xl 2xl:max-w-4xl">
            <Card>
                <div class="space-y-4">
                    <TextInput
                        v-model="form.name"
                        name="name"
                        :label="$t('global', 'Name')"
                    />

                    <Multiselect
                        v-model="form.type"
                        name="type"
                        :options="types"
                        mode="single"
                        :label="$t('global', 'Type')"
                    />

                    <div v-if="form.type === 'etsy'">
                        <TextInput
                            v-model="form.key.client_id"
                            name="key.client_id"
                            :label="$t('global', 'Client ID')"
                        />

                        <TextInput
                            v-model="form.key.shop_id"
                            name="key.shop_id"
                            :label="$t('global', 'Shop ID')"
                        />
                    </div>

                    <div v-if="form.type === 'shopify'">
                        <TextInput
                            v-model="form.key.domain"
                            name="key.domain"
                            :label="$t('global', 'Domain')"
                        />
                        <TextInput
                            v-model="form.key.access_token"
                            name="key.access_token"
                            :label="$t('global', 'Access Token')"
                        />
                    </div>

                    <div v-if="form.type === 'amazon'">
                        <TextInput
                            v-model="form.key.client_id"
                            name="key.client_id"
                            :label="$t('global', 'Client ID')"
                        />
                        <TextInput
                            v-model="form.key.client_secret"
                            name="key.client_secret"
                            :label="$t('global', 'Client Secret')"
                        />
                        <TextInput
                            v-model="form.key.refresh_token"
                            name="key.refresh_token"
                            :label="$t('global', 'Refresh Token')"
                        />
                        <Multiselect
                            v-model="form.key.region"
                            :options="[
                                { value: 'na', label: 'North America' },
                                { value: 'eu', label: 'Europe' },
                                { value: 'fe', label: 'Far East' },
                            ]"
                            name="key.region"
                            mode="single"
                            :label="$t('global', 'Region')"
                        />
                        <Multiselect
                            v-model="form.key.marketplace_ids"
                            name="key.marketplace_ids"
                            :options="[
                                // North America
                                {
                                    value: 'A2EUQ1WTGCTBG2',
                                    label: 'Canada (CA)',
                                },
                                {
                                    value: 'ATVPDKIKX0DER',
                                    label: 'United States (US)',
                                },
                                {
                                    value: 'A1AM78C64UM0Y8',
                                    label: 'Mexico (MX)',
                                },
                                {
                                    value: 'A2Q3Y263D00KWC',
                                    label: 'Brazil (BR)',
                                },
                                // Europe
                                {
                                    value: 'A1RKKUPIHCS9HS',
                                    label: 'Spain (ES)',
                                },
                                {
                                    value: 'A1F83G8C2ARO7P',
                                    label: 'United Kingdom (UK)',
                                },
                                {
                                    value: 'A13V1IB3VIYZZH',
                                    label: 'France (FR)',
                                },
                                {
                                    value: 'AMEN7PMS3EDWL',
                                    label: 'Belgium (BE)',
                                },
                                {
                                    value: 'A1805IZSGTT6HS',
                                    label: 'Netherlands (NL)',
                                },
                                {
                                    value: 'A1PA6795UKMFR9',
                                    label: 'Germany (DE)',
                                },
                                { value: 'APJ6JRA9NG5V4', label: 'Italy (IT)' },
                                {
                                    value: 'A2NODRKZP88ZB9',
                                    label: 'Sweden (SE)',
                                },
                                {
                                    value: 'AE08WJ6YKNBMC',
                                    label: 'South Africa (ZA)',
                                },
                                {
                                    value: 'A1C3SOZRARQ6R3',
                                    label: 'Poland (PL)',
                                },
                                { value: 'ARBP9OOSHTCHU', label: 'Egypt (EG)' },
                                {
                                    value: 'A33AVAJ2PDY3EV',
                                    label: 'Turkey (TR)',
                                },
                                {
                                    value: 'A17E79C6D8DWNP',
                                    label: 'Saudi Arabia (SA)',
                                },
                                {
                                    value: 'A2VIGQ35RCS4UG',
                                    label: 'United Arab Emirates (AE)',
                                },
                                { value: 'A21TJRUUN4KGV', label: 'India (IN)' },
                                // Far East
                                {
                                    value: 'A19VAU5U5O7RUS',
                                    label: 'Singapore (SG)',
                                },
                                {
                                    value: 'A39IBJ37TRP1C6',
                                    label: 'Australia (AU)',
                                },
                                {
                                    value: 'A1VC38T7YXB528',
                                    label: 'Japan (JP)',
                                },
                            ]"
                            :label="$t('global', 'Marketplace IDs')"
                        />

                        <label
                            for="additional_data"
                            class="flex items-center justify-between gap-2 text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            {{ $t('global', 'Additional Data') }}
                        </label>
                        <pre>{{ form.additional_data }}</pre>
                    </div>
                </div>
            </Card>
        </div>
    </PageContent>
</template>

<script setup lang="ts">
import {
    Card,
    Multiselect,
    PageContent,
    TextInput,
} from 'craftable-pro/Components';
import { InertiaForm } from 'craftable-pro/types';
import { watch } from 'vue';
import type { ApiKeyForm } from './types';

interface Props {
    form: InertiaForm<ApiKeyForm>;
    submit: void;
    types: string[];
}

const props = defineProps<Props>();

watch(
    () => props.form.type,
    (type) => {
        props.form.key = {};
    },
);
</script>
