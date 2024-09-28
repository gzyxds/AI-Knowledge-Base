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
                            <span>参考图权重</span>
                            <el-popover
                                placement="right"
                                :width="200"
                                :show-arrow="false"
                                transition="custom-popover"
                                trigger="hover"
                                content="设置生成图片时垫图的权重，值越大越像垫图，取值范围0.5-2， 默认值1"
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
                                v-model="modelValue.iw"
                                :step="0.1"
                                :min="0.5"
                                :max="2"
                            />
                            <span>{{ modelValue.iw }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span>图片质量</span>
                            <el-popover
                                placement="right"
                                :width="200"
                                :show-arrow="false"
                                transition="custom-popover"
                                trigger="hover"
                                content="设置图片的质量，越大质量越高，取值范围0.25 - 1，默认值1"
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
                        <div class="flex gap-4 items-center mt-2">
                            <el-select
                              v-model="modelValue.q"
                              placeholder="请选择图片质量"
                            >
                                <el-option
                                  v-for="(item, index) in config.mj_quality"
                                  :key="index"
                                  :label="item"
                                  :value="index"
                                />
                            </el-select>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span>风格化值</span>
                            <el-popover
                                placement="right"
                                :width="200"
                                :show-arrow="false"
                                transition="custom-popover"
                                trigger="hover"
                                content="设置生成图片时的风格化程度，值越大，风格化的程度越高，取值范围0-1000， 默认值100"
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
                                v-model="modelValue.s"
                                :step="1"
                                :max="1000"
                                :min="0"
                            />
                            <span>{{ modelValue.s }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span>混乱值</span>
                            <el-popover
                                placement="right"
                                :width="200"
                                :show-arrow="false"
                                transition="custom-popover"
                                trigger="hover"
                                content="本参数会控制生成4张图的差别， 值越大，生成4张图的区别越大，值越小,生成的4张图越接近，取值范围0-100， 默认值0"
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
                                v-model="modelValue.c"
                                :step="1"
                                :max="100"
                                :min="0"
                            />
                            <span>{{ modelValue.c }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span>随机种子</span>
                            <el-popover
                                placement="right"
                                :width="200"
                                :show-arrow="false"
                                transition="custom-popover"
                                trigger="hover"
                                content="种子用于指定生成效果，可以用于生成套图，保障生成的一系列图片保持同一种风格"
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
                                placeholder="请输入seed种子编号"
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
import { checkUserLogin, config } from './../../hooks/useDrawEffect'

const emit = defineEmits<{
    (event: 'update:modelValue', value: ComplexParams): void
}>()

const props = defineProps({
    modelValue: {
        type: Object as () => ComplexParams,
        default: {
            seed: '', // 随机种子
            iw: 1,
            q: 1,
            s: 100,
            c: 0
        }
    }
})

const { modelValue } = useVModels(props, emit)

const activeNames = ref<string>('1')
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
    :deep(.el-input) {
        @apply bg-page;
        border-radius: 4px;
        .el-input__wrapper {
            @apply bg-page;
            box-shadow: none;
        }
    }
    :deep(.el-select) {
        @apply bg-page;
        box-shadow: none;
        border-radius: 12px;
        .el-select__wrapper {
            @apply bg-page;
            box-shadow: none;
        }
    }
}
</style>