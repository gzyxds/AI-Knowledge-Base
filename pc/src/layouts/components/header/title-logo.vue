<template>
    <NuxtLink
        v-if="logo || title"
        to="/"
        class="flex items-center title-logo px-[10px]">
        <div v-if="logo">
            <img class="w-[34px] h-[34px]" :src="logo" />
        </div>
        <div v-if="title" class="font-bold ml-[10px] text-[16px] line-clamp-1">
            {{ title }}
        </div>
    </NuxtLink>
</template>
<script lang="ts" setup>
const props = defineProps<{
    logo: string
    title: string
}>()
</script>

<style lang="scss" scoped></style>
