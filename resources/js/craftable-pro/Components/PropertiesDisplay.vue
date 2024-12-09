<template>
    <div class="properties__wrap flex flex-wrap gap-4">
        <div v-for="(item, index) in formatProperties(properties)" :key="index">
            <strong class="capitalize">{{ item.key }}:</strong>
            <div
                v-for="(el, key) in JSON.parse(item.value)"
                :key="key"
                class="properties__item flex gap-2"
            >
                <strong class="capitalize">{{ key }}:</strong>
                <pre class="whitespace-pre-wrap">{{ el }}</pre>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
interface Props {
    properties: object;
}
const props = defineProps<Props>();

function formatProperties(properties) {
    const formatted = [];
    for (const [key, value] of Object.entries(properties)) {
        if (typeof value === 'object' && value !== null) {
            formatted.push({ key, value: JSON.stringify(value, null, 2) });
        } else {
            formatted.push({ key, value });
        }
    }
    return formatted;
}
</script>
<style scoped lang="scss">
.properties {
    &__wrap {
    }
    &__item {
    }
}
</style>
