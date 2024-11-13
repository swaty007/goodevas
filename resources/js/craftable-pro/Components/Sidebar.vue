<template>
  <div>
    <nav class="mt-5 space-y-1">
      <SidebarGroup :title="$t('craftable-pro', 'Content')">
<!--        <SidebarItem-->
<!--          :href="route('craftable-pro.media.index')"-->
<!--          :icon="PhotoIcon"-->
<!--          v-can="'craftable-pro.media.index'"-->
<!--        >-->
<!--          {{ $t("craftable-pro", "Media") }}-->
<!--        </SidebarItem>-->
        <SidebarItem
                        :href="route('craftable-pro.products.index')"
                        :icon="CubeTransparentIcon"
                        v-can="'global.product.index'"
                    >
                        {{ $t("global", "Products") }}
                    </SidebarItem>

          <SidebarItem
              :href="route('craftable-pro.products.index-income')"
              :icon="CubeTransparentIcon"
              v-can="'global.product.index'"
          >
              {{ $t("global", "Products Income") }}
          </SidebarItem>
          <SidebarItem
              :href="route('craftable-pro.products.index-api')"
              :icon="CubeTransparentIcon"
              v-can="'global.product.index'"
          >
              {{ $t("global", "Products Api") }}
          </SidebarItem>
                    <SidebarItem
                        :href="route('craftable-pro.product-types.index')"
                        :icon="CubeTransparentIcon"
                        v-can="'global.product-type.index'"
                    >
                        {{ $t("global", "Product Types") }}
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
          {{ $t("craftable-pro", "Access") }}
        </SidebarItem>
        <SidebarItem
          :href="route('craftable-pro.roles.index')"
          :icon="KeyIcon"
          v-can="'craftable-pro.role.index'"
        >
          {{ $t("craftable-pro", "Roles") }}
        </SidebarItem>
          <SidebarItem
              :href="route('craftable-pro.warehouses.index')"
              :icon="CubeTransparentIcon"
              v-can="'global.warehouse.index'"
          >
              {{ $t("global", "Warehouses") }}
          </SidebarItem>
          <SidebarItem
              :href="route('craftable-pro.ysells.index')"
              :icon="CubeTransparentIcon"
              v-can="'global.ysell.index'"
          >
              {{ $t("global", "Ysells") }}
          </SidebarItem>
        <SidebarItem
          :href="route('craftable-pro.translations.index')"
          :icon="LanguageIcon"
          v-can="'craftable-pro.translation.index'"
        >
          {{ $t("craftable-pro", "Localization") }}
        </SidebarItem>
        <SidebarItem
          :href="route('craftable-pro.settings.index')"
          :icon="Cog8ToothIcon"
          v-can="'craftable-pro.settings.edit'"
        >
          {{ $t("craftable-pro", "Settings") }}
        </SidebarItem>

          <SidebarItem
              :href="route('craftable-pro.activity.index')"
              :icon="InboxStackIcon"
              v-can="'craftable-pro.settings.edit'"
          >
              {{ $t("craftable-pro", "Logs") }}
          </SidebarItem>
      </SidebarGroup>
    </nav>
  </div>
</template>

<script setup lang="ts">
import {
  KeyIcon,
  LanguageIcon,
  PhotoIcon,
  UsersIcon,
  Cog8ToothIcon,
  CubeTransparentIcon,
    InboxStackIcon
} from "@heroicons/vue/24/outline";
import { SidebarItem, SidebarGroup } from "craftable-pro/Components";
</script>
