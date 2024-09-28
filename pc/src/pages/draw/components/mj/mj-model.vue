<template>
    <div>
        <sidbar-item-title
            title="模型选择"
            required
            tips="指定midjourney的渲染模型"
        />

        <div class="grid grid-cols-2 gap-4">
            <div
                v-for="(item, index) in modelList"
                :key="item.cover"
                class="flex flex-col gap-2"
                @click="currentModel = item.value"
            >
                <div class="relative rounded-[12px] overflow-hidden">
                    <aspect-ratio
                        class="rounded-[12px] overflow-hidden bg-[var(--el-bg-color-page)]"
                        :src="item.cover"
                        fit="cover"
                        :ratio="[144, 100]"
                    />
                    <div
                        class="absolute top-0 left-0 bg-[rgba(0,0,0,0.4)] w-full h-full flex justify-center items-center transition-opacity opacity-0"
                        :class="{
                            'opacity-100': item.value === currentModel
                        }"
                    >
                        <Icon
                            name="el-icon-CircleCheckFilled"
                            :size="20"
                            color="#fff"
                        />
                    </div>
                </div>
                <div class="text-hidden-2 text-center">
                    {{ item.title }}
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { useVModels } from '@vueuse/core'
import sidbarItemTitle from './../common/sidbar-item-title.vue'
import MjImage from '~/assets/image/draw/mj.png'
import NjImage from '~/assets/image/draw/nj.png'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: ''
    }
)

const { modelValue: currentModel } = useVModels(props, emit)

const modelList = [
    {
        value: 'mj',
        title: '真实感强',
        cover: MjImage
    },
    {
        value: 'niji',
        title: '卡通动漫',
        cover: NjImage
    }
]
</script>