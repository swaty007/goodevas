<template>
    <PageContent>
        <div class="mx-auto max-w-3xl 2xl:max-w-4xl">
            <Card>
                <div class="space-y-4">
                    <TextInput
                        v-model="form.api_key"
                        name="api_key"
                        :label="$t('global', 'Api Key')"
                    />

                    <TextInput
                        v-model="form.name"
                        name="name"
                        :label="$t('global', 'Name')"
                    />
                </div>
            </Card>
        </div>
    </PageContent>
</template>

<script setup lang="ts">
import { Card, PageContent, TextInput } from 'craftable-pro/Components';
import { InertiaForm } from 'craftable-pro/types';
import type { YsellForm } from './types';

interface Props {
    form: InertiaForm<YsellForm>;
    submit: void;
}

const props = defineProps<Props>();
</script>
