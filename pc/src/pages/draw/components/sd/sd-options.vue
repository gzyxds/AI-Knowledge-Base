<template>
    <div>
        <el-collapse v-model="activeNames" class="complex_params">
            <el-collapse-item title="高级参数" name="1">
                <template #title>
                    <div class="flex items-center gap-2">
                        <!-- <Icon name="el-icon-Operation"></Icon> -->
                        <span>高级参数</span>
                    </div>
                </template>
                <div class="flex flex-col gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span>绘画步数</span>
                            <el-popover
                                placement="right"
                                :width="200"
                                :show-arrow="false"
                                transition="custom-popover"
                                trigger="hover"
                                content="越低：细节简练，耗时更短；越高：细节丰富，耗时变长；注*步数过高可能产生细节扭曲"
                            >
                                <template #reference>
                                    <div
                                        class="flex items-center cursor-pointer text-[#999999]"
                                    >
                                        <Icon
                                            name="el-icon-QuestionFilled"
                                            :size="14"
                                        />
                                    </div>
                                </template>
                            </el-popover>
                        </div>
                        <div class="flex gap-4 items-center pl-3">
                            <el-slider
                                v-model="modelValue.step"
                                :step="1"
                                :max="150"
                            />
                            <span>{{ modelValue.step }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span>文本强度</span>
                            <el-popover
                                placement="right"
                                :show-arrow="false"
                                transition="custom-popover"
                                :width="200"
                                trigger="hover"
                                content="低：淡化输入的特征，淡化风格；高：强化输入的特征，强化风格；最佳使用区间7-12，推荐不超过15"
                            >
                                <template #reference>
                                    <div
                                        class="flex items-center cursor-pointer text-[#999999]"
                                    >
                                        <Icon
                                            name="el-icon-QuestionFilled"
                                            :size="14"
                                        />
                                    </div>
                                </template>
                            </el-popover>
                        </div>
                        <div class="flex gap-4 items-center pl-3">
                            <el-slider
                                v-model="modelValue.cfg_scale"
                                :step="0.5"
                                :max="30"
                            />
                            <span>{{ modelValue.cfg_scale }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span>采样模式</span>
                            <el-popover
                                placement="right"
                                :show-arrow="false"
                                transition="custom-popover"
                                :width="200"
                                trigger="hover"
                                content="靠前的采样（euler）：适合动漫，细节简练，快速；靠后的采样（DPM）：适合写实，细节丰富，较慢"
                            >
                                <template #reference>
                                    <div
                                        class="flex items-center cursor-pointer text-[#999999]"
                                    >
                                        <Icon
                                            name="el-icon-QuestionFilled"
                                            :size="14"
                                        />
                                    </div>
                                </template>
                            </el-popover>
                        </div>
                        <div class="flex gap-4 items-center">
                            <el-select
                                v-model="modelValue.sampler_name"
                                placeholder="请选择采样模式"
                            >
                                <el-option
                                    v-for="item in samplersList"
                                    :key="item.name"
                                    :label="item.name"
                                    :value="item.name"
                                />
                            </el-select>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span>随机种子</span>
                            <el-popover
                                placement="right"
                                :show-arrow="false"
                                transition="custom-popover"
                                :width="200"
                                trigger="hover"
                                content="每次生成图的初始画布，种子、提示词、参数和模型相同的情况下，可复原绘画结果"
                            >
                                <template #reference>
                                    <div
                                        class="flex items-center cursor-pointer text-[#999999]"
                                    >
                                        <Icon
                                            name="el-icon-QuestionFilled"
                                            :size="14"
                                        />
                                    </div>
                                </template>
                            </el-popover>
                        </div>
                        <div class="flex gap-4 items-center">
                            <el-input
                                v-model="modelValue.seed"
                                type="number"
                                :min="-1"
                                :maxlength="18"
                                @focus="checkUserLogin()"
                                placeholder="请选择采样模式"
                            />
                        </div>
                    </div>
                </div>
            </el-collapse-item>
        </el-collapse>
    </div>
</template>

<script lang="ts" setup>
import { ComplexParams, samplersItem } from '../../types/draw'
import { useVModels } from '@vueuse/core'
import { getSdSamplersList } from '@/api/draw'
import { checkUserLogin } from './../../hooks/useDrawEffect'

const emit = defineEmits<{
    (event: 'update:modelValue', value: ComplexParams): void
}>()

const props = defineProps({
    modelValue: {
        type: Object as () => ComplexParams,
        default: {
            step: '', // 采样步数
            sampling: '', // 采样模式
            seed: '', // 随机种子
            cfg: '' // 提示词系数
        }
    }
})

const { modelValue } = useVModels(props, emit)

const samplersList = ref<samplersItem[]>([])

const activeNames = ref<string>('1')

onMounted(async () => {
    getSdSamplersList().then((res) => {
        samplersList.value = res
    })
})
</script>

<style lang="scss" scoped>
.complex_params {
    :deep(.el-collapse-item) {
        .el-collapse-item__wrap {
            border: none !important;
            .el-collapse-item__content {
                padding-bottom: 0;
            }
        }
        .el-collapse-item__header {
            border: none !important;
        }
    }
}
</style>
