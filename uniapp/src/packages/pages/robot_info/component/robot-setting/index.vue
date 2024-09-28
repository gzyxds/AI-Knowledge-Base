<template>
    <view class="h-full pt-[20rpx]">
        <view class="h-full bg-white flex flex-col py-[20rpx]">
            <view class="flex mb-[20rpx] px-[20rpx]">
                <view class="bg-page p-[8rpx] flex rounded-[8rpx]">
                    <view
                        v-for="item in tabState.list"
                        :key="item.type"
                        class="px-[14rpx] py-[9rpx] rounded-[8rpx]"
                        :class="{
                            'tab-active': item.type == tabState.current
                        }"
                        @click="tabsChange(item.type)"
                    >
                        {{ item.name }}
                    </view>
                </view>
            </view>
            <view class="flex-1 min-h-0 relative z-10">
                <scroll-view class="h-full" scroll-y :scroll-top="scrollTop">
                    <view class="px-[20rpx]">
                        <u-form
                            :model="robotInfo"
                            ref="uFormRef"
                            :rules="rules"
                            label-position="top"
                            :border-bottom="false"
                        >
                            <BaseConfig
                                v-model="robotInfo"
                                v-show="tabState.current == 'base'"
                            />
                            <SearchConfig
                                v-model="robotInfo"
                                v-show="tabState.current == 'search'"
                            />
                            <InterfaceConfig
                                v-model="robotInfo"
                                v-if="tabState.current == 'interface'"
                            />
                            <DigitalConfig
                                v-model="robotInfo"
                                v-show="tabState.current == 'digital'"
                            />
                        </u-form>
                    </view>
                </scroll-view>
            </view>
            <view class="px-[20rpx] pt-[20rpx]">
                <view class="flex-1">
                    <u-button
                        type="primary"
                        :custom-style="{ 'z-index': 0 }"
                        @click="handelSave"
                    >
                        保存
                    </u-button>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { nextTick, reactive, ref, shallowRef } from 'vue'
import BaseConfig from './base-config.vue'
import SearchConfig from './search-config.vue'
import InterfaceConfig from './interface-config.vue'
import DigitalConfig from './digital-config.vue'
import { putRobot } from '@/api/robot'
import { onMounted } from 'vue'
import { useRobot } from '../../useRobot'
import { useRouter } from 'uniapp-router-next'

const router = useRouter()
const tabState = reactive({
    list: [
        {
            name: '基本设置',
            type: 'base'
        },
        {
            name: 'AI模型/搜索配置',
            type: 'search'
        },
        {
            name: '界面设置',
            type: 'interface'
        },
        {
            name: '形象设置',
            type: 'digital'
        }
    ],
    current: 'base'
})
const scrollTop = ref(0)
const tabsChange = (type: string) => {
    tabState.current = type
    scrollTop.value = 10
    nextTick(() => {
        scrollTop.value = 0
    })
}
const uFormRef = shallowRef()
const { robotInfo, getRobotInfo } = useRobot()
const rules = {
    image: [
        {
            required: true,
            message: '请选择智能体图标'
        }
    ],
    name: [
        {
            required: true,
            message: '请输入智能体名称',
            trigger: ['change']
        }
    ],
    model_sub_id: [
        {
            required: true,
            message: '请选择AI模型'
        }
    ],
    cate_id: [
        {
            required: true,
            message: '请选择智能体分类'
        }
    ],
    digital_id: [
        {
            required: true,
            message: '请选择形象',
            validator: (rule: any, value: any, callback: any) => {
                if (value > 0) {
                    return true
                } else {
                    return false
                }
            }
        }
    ]
}

const handelSave = () => {
    uFormRef.value.validate(async (valid: boolean) => {
        if (valid) {
            await putRobot(robotInfo.value)
            getRobotInfo()
            setTimeout(() => {
                router.navigateBack()
            }, 1000)
        }
    })
}

onMounted(() => {
    setTimeout(() => {
        uFormRef.value.setRules(rules)
    }, 10)
})
</script>
<style lang="scss">
.tab-active {
    background: linear-gradient(
        90deg,
        var(--color-minor) 0%,
        var(--color-primary) 100%
    );
    @apply text-white;
}
</style>
