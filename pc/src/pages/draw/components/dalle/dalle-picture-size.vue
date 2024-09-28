<template>
    <div class="mt-[15px]">
        <sidbar-item-title
            title="图片尺寸"
            tips=""
            required
        />
        <div class="flex flex-wrap">
            <el-popover
                v-for="(item, index) in pictureSize.lists"
                :key="index"
                placement="bottom"
                :width="150"
                :show-arrow="false"
                transition="custom-popover"
                trigger="hover"
                :content="`分辨率：${item.scaleValue}px`"
            >
                <template #reference>
                    <div
                        class="picture-size cursor-pointer text-center hover:text-primary"
                        :class="{
                            'picture-size-active': value == item?.scaleValue,
                            'picture-size-disable': !item?.scaleValue
                        }"
                        @click="value = item.scaleValue"
                    >
                        <div
                            class="flex justify-center items-center mt-[10px] h-[20px]"
                        >
                            <div class="rect" :class="item.class" />
                        </div>
                        <div
                            class="text-base text-[#101010] dark:text-white mt-[4px] size-scale"
                        >
                            {{ item.scaleLabel }}
                        </div>
                        <div
                            class="text-xs text-[#798696] dark:text-white mt-[4px] size-name"
                        >
                            {{ item.name }}
                        </div>
                    </div>
                </template>
            </el-popover>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import sidbarItemTitle from './../common/sidbar-item-title.vue'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: '512x512'
    }
)
const { modelValue: value } = useVModels(props, emit)

const pictureSize = reactive({
    lists: [
        {
            name: '头像图',
            scaleLabel: '1:1',
            scaleValue: '1024x1024',
            class: 'w-[20px] h-[20px]'
        },
        {
            name: '媒体配图',
            scaleLabel: '3:4',
            scaleValue: '1024x1792',
            class: 'w-[15px] h-[20px]'
        },
        {
            name: '文章配图',
            scaleLabel: '4:3',
            scaleValue: '1792x1024',
            class: 'w-[20px] h-[15px]'
        }
    ]
})

value.value = '1024x1024'
</script>

<style lang="scss" scoped>
// 图片尺寸
.picture-size {
    transition: all 0.2s;
    border: 1px solid transparent;
    user-select: none;
    width: 70px;
    height: 84px;
    margin-top: 10px;
    margin-right: 14px;
    border-radius: 12px;
    background-color: var(--el-bg-color-page);

    .rect {
        background-color: #b3bcc8;
    }
}

.picture-size:hover {
    border: 1px solid var(--el-color-primary);
    background-color: var(--el-color-primary-light-9);

    .rect {
        background-color: var(--el-color-primary);
    }

    .size-scale,
    .size-name {
        color: var(--el-color-primary);
    }
}

.picture-size-active {
    border: 1px solid var(--el-color-primary);
    background-color: var(--el-color-primary-light-9);

    .rect {
        background-color: var(--el-color-primary);
    }

    .size-scale,
    .size-name {
        color: var(--el-color-primary);
    }
}

.picture-size-disable {
    filter: opacity(0.5);
    user-select: none;
    pointer-events: none;
    cursor: not-allowed;
}
</style>