<template>
    <div
        v-if="url"
        class="flex mb-[16px] bg-body rounded-[5px] p-[10px] max-w-[300px] items-center"
    >
        <el-image
            class="flex-none w-[40px] h-[40px]"
            :src="url"
            :preview-src-list="[url]"
            :hide-on-click-modal="true"
        />
        <div
            class="line-clamp-2 flex-1 min-w-0 ml-[6px] text-tx-primary"
            :style="{
                'word-break': 'break-word'
            }"
        >
            {{ name }}
        </div>
    </div>
</template>

<script lang="ts" setup>
const props = defineProps<{
    url: string
    name: string
}>()
</script>
