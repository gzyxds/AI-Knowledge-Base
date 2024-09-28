<template>
    <div style="position: relative" :style="ratioStr">
        <div style="position: absolute; inset: 0px; user-select: none">
            <el-image
                v-if="type === 'image'"
                class="!block h-[100%]"
                :src="thumbnail || src"
                :alt="alt || src"
                :lazy="lazy"
                :fit="fit"
                :preview-src-list="preview ? [src] : []"
            />
            <video-player
                v-else
                :src="src"
                width="100%"
                height="100%"
                :controlBtns="['audioTrack', 'quality', 'volume', 'fullScreen']"
            />
        </div>
    </div>
</template>

<script lang="ts" setup>
const props = defineProps({
    preview: {
        type: Boolean,
        default: true
    },
    ratio: {
        type: Array as unknown as () => [number, number],
        default: () => [1, 1]
    },
    alt: {
        type: String,
        default: ''
    },
    type: {
        type: String as () => 'image' | 'video',
        default: 'image'
    },
    src: {
        type: String,
        default: ''
    },
    thumbnail: {
        type: String,
        default: ''
    },
    lazy: {
        type: Boolean,
        default: true
    },
    fit: {
        type: String as PropType<'contain' | 'cover'>,
        default: 'contain'
    }
})

const ratioStr = computed(() => {
    const [width, paddingBottom] = props.ratio
    return `width: 100%;padding-bottom: ${(paddingBottom / width) * 100}%;`
})
</script>

<style lang="scss" scoped></style>
