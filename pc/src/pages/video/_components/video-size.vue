<template>
    <el-form-item prop="scale" required>
        <template #label>
            <span class="font-bold text-tx-primary"> 生成尺寸 </span>
        </template>
        <div class="flex-1 min-w-0 overflow-hidden">
            <div class="flex flex-wrap mx-[-6px] mb-[-10px]">
                <div
                    v-for="(item, index) in pictureSize.lists"
                    :key="index"
                    class="w-[33.3%] px-[6px]"
                >
                    <div
                        class="picture-size cursor-pointer text-center hover:text-primary"
                        :class="{
                            'picture-size-active': value == item?.scaleValue,
                            'picture-size-disable': !item?.scaleValue
                        }"
                        @click="value = item.scaleValue"
                    >
                        <div class="flex justify-center items-center h-[20px]">
                            <div class="rect" :class="item.class" />
                        </div>
                        <div
                            class="text-base text-[#101010] dark:text-white mt-[4px] size-scale"
                        >
                            {{ item.scaleValue }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </el-form-item>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const props = withDefaults(
    defineProps<{
        modelValue?: any
    }>(),
    {
        modelValue: '1:1'
    }
)
const { modelValue: value } = useVModels(props, emit)

const pictureSize = reactive({
    lists: [
        {
            scaleValue: '1:1',
            class: 'w-[20px] h-[20px]'
        },
        {
            scaleValue: '3:4',
            class: 'w-[15px] h-[20px]'
        },
        {
            scaleValue: '4:3',
            class: 'w-[20px] h-[15px]'
        },
        {
            scaleValue: '9:16',
            class: 'w-[13px] h-[20px]'
        },
        {
            scaleValue: '16:9',
            class: 'w-[20px] h-[12px]'
        }
    ]
})

value.value = '1:1'
</script>

<style lang="scss" scoped>
// 图片尺寸
.picture-size {
    transition: all 0.2s;
    border: 1px solid transparent;
    user-select: none;

    margin-bottom: 10px;
    border-radius: 12px;
    padding: 14px;
    line-height: 1.5;
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
