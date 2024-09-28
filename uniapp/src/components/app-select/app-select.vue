<template>
    <view class="app-select">
        <view
            class="input flex"
            :class="{
                'bg-[#f8f8f8] pointer-events-none': disabled
            }"
            @click.stop="show = true"
        >
            <view class="flex-1 flex items-center">
                <view v-if="shouldShowPlaceholder" class="input-placeholder">{{
                    placeholder
                }}</view>
                <view class="flex flex-wrap flex-1" v-if="type == 'checkbox'">
                    <view
                        class="mr-[10rpx]"
                        v-for="(item, index) in checkboxData"
                        :key="item[value]"
                    >
                        <u-tag
                            :text="item[name]"
                            size="mini"
                            mode="light"
                            closeable
                            @close="removeSelect(item)"
                        />
                    </view>
                </view>
                <view
                    class="flex flex-1"
                    v-if="type == 'radio' && radioData[name]"
                >
                    <view class="flex-1 min-w-0">
                        <slot name="label" :item="radioData">
                            <view class="line-clamp-1">
                                {{ radioData[name] }}
                            </view>
                        </slot>
                    </view>

                    <view
                        class="text-muted flex mx-[20rpx]"
                        v-if="radioData[name] && closeable"
                        @click.stop="$emit('update:modelValue', '');$emit('change', '')"
                    >
                        <u-icon
                            name="close-circle
"
                        />
                    </view>
                </view>
            </view>
            <view class="text-muted flex">
                <u-icon name="arrow-down" />
            </view>
        </view>
        <u-popup
            :modelValue="show"
            mode="bottom"
            @close="cancel"
            safe-area-inset-bottom
        >
            <view class="title">{{ popupTitle }}</view>
            <view style="padding: 20rpx">
                <u-search
                    v-if="filter"
                    placeholder="输入关键词搜索"
                    :show-action="false"
                    v-model="keyword"
                ></u-search>

                <scroll-view
                    :scroll-top="scrollTop"
                    scroll-y="true"
                    class="scroll-Y mt-[20rpx]"
                    @scrolltolower="$emit('lower')"
                >
                    <!--单选-->
                    <u-radio-group
                        v-if="type == 'radio'"
                        wrap
                        width="100%"
                        v-model="radioValue"
                    >
                        <u-radio
                            v-for="(item, index) in filterOptions"
                            :key="index"
                            :name="item[value]"
                        >
                            <slot name="label" :item="item">
                                {{ item[name] }}
                            </slot>
                        </u-radio>
                    </u-radio-group>
                    <!--多选-->
                    <u-checkbox-group
                        v-if="type == 'checkbox'"
                        wrap
                        width="100%"
                        v-model="checkboxValue"
                    >
                        <u-checkbox
                            v-for="(item, index) in filterOptions"
                            :key="index"
                            :name="item[value]"
                            :modelValue="
                                !!checkboxValue.some((v) => v == item[value])
                            "
                        >
                            <slot name="label" :item="item">
                                {{ item[name] }}
                            </slot>
                        </u-checkbox>
                    </u-checkbox-group>
                </scroll-view>
                <view class="bottons flex">
                    <view class="flex-1 mr-[20rpx]">
                        <u-button @click="cancel">取消</u-button>
                    </view>
                    <view class="flex-1">
                        <u-button
                            @click="submit"
                            type="primary"
                            throttleTime="1000"
                        >
                            确认
                        </u-button>
                    </view>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script>
export default {
    options: {
        styleIsolation: 'shared',
        multipleSlots: true // 在组件定义时的选项中启用多slot支持
    },
    props: {
        dataLists: {
            default: () => [],
            type: Array
        },
        name: {
            default: 'name'
        },
        value: {
            default: 'value'
        },
        modelValue: {
            type: [String, Number, Array]
        },
        type: {
            default: 'radio',
            type: String
        },

        popupTitle: {
            default: '列表选择',
            type: String
        },
        placeholder: {
            default: '请输入搜索内容'
        },
        filter: {
            default: true,
            type: Boolean
        },
        disabled: {
            default: false,
            type: Boolean
        },
        closeable: {
            default: true,
            type: Boolean
        }
    },
    computed: {
        filterOptions() {
            if (!this.filter) {
                return this.dataLists
            }
            return this.dataLists.filter((item) =>
                item[this.name].includes(this.keyword)
            )
        },
        shouldShowPlaceholder() {
            if (Array.isArray(this.modelValue)) {
                return this.modelValue.length == 0
            }
            if (
                this.modelValue === undefined ||
                this.modelValue === null ||
                this.modelValue === ''
            ) {
                return true
            }
            return false
        },
        checkboxData() {
            if (this.type !== 'checkbox' || !Array.isArray(this.modelValue)) {
                return []
            }
            const result = []
            this.modelValue.forEach((val) => {
                const item = this.dataLists.find(
                    (item) => item[this.value] == val
                )
                if (item) {
                    result.push(item)
                } else {
                    result.push({
                        [this.name]: val,
                        [this.value]: val
                    })
                }
            })
            return result
        },
        radioData() {
            if (this.type !== 'radio' || Array.isArray(this.modelValue)) {
                return {}
            }
            const item = this.dataLists.find(
                (item) => item[this.value] === this.modelValue
            )
            return (
                item || {
                    [this.name]: this.modelValue,
                    [this.value]: this.modelValue
                }
            )
        }
    },
    data() {
        return {
            show: false,
            keyword: '',
            scrollTop: 0,
            checkboxValue: [],
            radioValue: ''
        }
    },
    watch: {
        modelValue: {
            handler(newVal, oldVal) {
                if (this.type == 'radio') {
                    this.radioValue = newVal
                } else if (this.type == 'checkbox') {
                    this.checkboxValue = (newVal || []).slice()
                }
            },
            immediate: true
        },
        show(newVal) {
            if (newVal) {
                this.keyword = ''
                if (this.type == 'radio') {
                    this.radioValue = this.modelValue
                } else if (this.type == 'checkbox') {
                    this.checkboxValue = (this.modelValue || []).slice()
                }
            }
        }
    },
    methods: {
        removeSelect(item) {
            const index = this.checkboxValue.findIndex(
                (value) => value == item[this.value]
            )
            if (index > -1) {
                this.checkboxValue.splice(index, 1)
                this.$emit('change', this.checkboxValue.slice())
                this.$emit('update:modelValue', this.checkboxValue.slice())
            }
        },

        //点击取消按钮触发
        cancel() {
            this.show = false
        },
        //提交触发
        submit() {
            if (this.type == 'radio') {
                this.$emit('update:modelValue', this.radioValue)
            } else if (this.type == 'checkbox') {
                this.$emit('update:modelValue', this.checkboxValue.slice())
            }
            this.$emit('change')
            this.cancel()
        }
    }
}
</script>

<style lang="scss" scoped>
.app-select {
    width: 100%;
    .title {
        border-bottom: 1px solid #f7f7f7;
        padding: 15rpx 20rpx;

        font-weight: bold;
        font-size: 32rpx;
    }

    .scroll-Y {
        height: 650rpx;
    }

    .bottons {
        padding: 20rpx 0 0;
    }
    :deep() {
        .u-checkbox-group {
            width: 100%;
            .u-checkbox {
                display: flex;
                flex-direction: row-reverse;
                padding: 10rpx 0;
                border-bottom: 1px solid #f7f7f7;
                .u-checkbox__label {
                    flex: 1;
                }
            }
        }

        .u-radio-group {
            width: 100%;
            .u-radio {
                display: flex;
                flex-direction: row-reverse;
                padding: 10rpx 0;
                border-bottom: 1px solid #f7f7f7;
                .u-radio__label {
                    flex: 1;
                }
            }
        }
    }
    .input {
        min-height: 70rpx;
        width: 100%;
        border-radius: 14rpx;
        line-height: 50rpx;
        border: 1px solid var(--color-light, #e5e5e5);
        &-placeholder {
            color: rgb(136, 136, 136);
        }
        padding: 10rpx 20rpx;
        box-sizing: border-box;
    }
}
</style>
