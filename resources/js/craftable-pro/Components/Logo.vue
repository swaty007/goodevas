<template>
  <div>
    <Link :href="route('craftable-pro.home')">
      <span class="sr-only">Craftable PRO</span>
       <h2><strong>{{ $t('global', 'Goodevas') }}</strong></h2>
    </Link>
  </div>
</template>

<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
</script>
