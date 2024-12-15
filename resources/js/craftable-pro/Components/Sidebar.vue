<template>
    <div>
        <nav class="mt-5 space-y-1">
            <SidebarGroup :title="$t('craftable-pro', 'Content')">
                <!--        <SidebarItem-->
                <!--          :href="route('craftable-pro.media.index')"-->
                <!--          :icon="PhotoIcon"-->
                <!--          v-can="'craftable-pro.media.index'"-->
                <!--        >-->
                <!--          <template v-if="!mini">{{ $t("craftable-pro", "Media") }}</template>-->
                <!--        </SidebarItem>-->
                <SidebarItem
                    :href="route('craftable-pro.products.index')"
                    :icon="CubeTransparentIcon"
                    v-can="'global.product.index'"
                >
                    <template v-if="!mini">{{
                        $t('global', 'Products')
                    }}</template>
                </SidebarItem>

                <SidebarItem
                    :href="route('craftable-pro.products.index-income')"
                    :icon="CubeTransparentIcon"
                    v-can="'global.product.index'"
                >
                    <template v-if="!mini">{{
                        $t('global', 'Products Income')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.products.index-api')"
                    :icon="CubeTransparentIcon"
                    v-can="'global.product.index'"
                >
                    <template v-if="!mini">{{
                        $t('global', 'Products Api')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.products.index-forecast')"
                    :icon="CubeTransparentIcon"
                    v-can="'global.product.index'"
                >
                    <template v-if="!mini">{{
                        $t('global', 'Forecast')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.product-types.index')"
                    :icon="CubeTransparentIcon"
                    v-can="'global.product-type.index'"
                >
                    <template v-if="!mini">{{
                        $t('global', 'Product Types')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.orders.index')"
                    :icon="CubeTransparentIcon"
                    v-can="'global.order.index'"
                >
                    {{ $t('global', 'Orders') }}
                </SidebarItem>
                <!--AppendGeneratorLink-->
            </SidebarGroup>

            <SidebarGroup
                :title="$t('craftable-pro', 'System')"
                v-can:any="[
                    'craftable-pro.craftable-pro-user.index',
                    'craftable-pro.role.index',
                    'craftable-pro.translation.index',
                    'craftable-pro.settings.edit',
                ]"
            >
                <SidebarItem
                    :href="route('craftable-pro.craftable-pro-users.index')"
                    :icon="UsersIcon"
                    v-can="'craftable-pro.craftable-pro-user.index'"
                >
                    <template v-if="!mini">{{
                        $t('craftable-pro', 'Access')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.roles.index')"
                    :icon="KeyIcon"
                    v-can="'craftable-pro.role.index'"
                >
                    <template v-if="!mini">{{
                        $t('craftable-pro', 'Roles')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.warehouses.index')"
                    :icon="CubeTransparentIcon"
                    v-can="'global.warehouse.index'"
                >
                    <template v-if="!mini">{{
                        $t('global', 'Warehouses')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.ysells.index')"
                    :icon="CubeTransparentIcon"
                    v-can="'global.ysell.index'"
                >
                    <template v-if="!mini">{{
                        $t('global', 'Ysells')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.api-keys.index')"
                    :icon="CubeTransparentIcon"
                    v-can="'global.api-key.index'"
                >
                    <template v-if="!mini">{{
                        $t('global', 'Api Keys')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.translations.index')"
                    :icon="LanguageIcon"
                    v-can="'craftable-pro.translation.index'"
                >
                    <template v-if="!mini">{{
                        $t('craftable-pro', 'Localization')
                    }}</template>
                </SidebarItem>
                <SidebarItem
                    :href="route('craftable-pro.settings.index')"
                    :icon="Cog8ToothIcon"
                    v-can="'craftable-pro.settings.edit'"
                >
                    <template v-if="!mini">{{
                        $t('craftable-pro', 'Settings')
                    }}</template>
                </SidebarItem>

                <SidebarItem
                    :href="route('craftable-pro.activity.index')"
                    :icon="InboxStackIcon"
                    v-can="'craftable-pro.settings.edit'"
                >
                    <template v-if="!mini">{{
                        $t('craftable-pro', 'Logs')
                    }}</template>
                </SidebarItem>
            </SidebarGroup>
        </nav>
    </div>
</template>

<script setup lang="ts">
import {
    Cog8ToothIcon,
    CubeTransparentIcon,
    InboxStackIcon,
    KeyIcon,
    LanguageIcon,
    UsersIcon,
} from '@heroicons/vue/24/outline';
import { SidebarGroup, SidebarItem } from 'craftable-pro/Components';
import { inject } from 'vue';

const mini = inject('mini');
</script>
