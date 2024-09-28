<template>
    <view>
        <u-form-item label="欢迎语" prop="welcome_introducer">
            <view class="flex-1">
                <u-input
                    v-model="formData.welcome_introducer"
                    type="textarea"
                    :height="200"
                    placeholder=""
                    :border="true"
                />
                <view class="text-muted">
                    打开聊天窗口后会主动发送，添加双井号可添加提问示例，例如：#帮我写一则关于xxx的文案#
                    <br />
                    多个问题请用回车换行
                </view>
            </view>
        </u-form-item>

        <u-form-item label="底部标识" prop="copyright">
            <view class="flex-1">
                <u-input
                    v-model="formData.copyright"
                    placeholder=""
                    :border="true"
                />
                <view class="text-muted"> 不填写不显示 </view>
            </view>
        </u-form-item>
        <u-form-item label="菜单设置">
            <view class="flex-1 min-w-0">
                <uni-table ref="table" stripe emptyText="暂无更多数据">
                    <uni-tr>
                        <uni-th width="100">关键词</uni-th>
                        <uni-th width="100">回复内容</uni-th>
                        <uni-th width="80">操作</uni-th>
                    </uni-tr>
                    <uni-tr
                        v-for="(item, index) in formData.menus"
                        :key="index"
                    >
                        <uni-td>{{ item.keyword }}</uni-td>
                        <uni-td>
                            <view class="flex">
                                <view v-if="item.content"> 文字 </view>
                                <view
                                    v-if="item.content && item.images?.length"
                                >
                                    +
                                </view>
                                <view v-if="item.images?.length"> 图片 </view>
                            </view>
                        </uni-td>
                        <uni-td>
                            <view class="flex">
                                <view
                                    class="text-primary mr-[20rpx]"
                                    @click="handleShowMenuPopup('edit', item)"
                                >
                                    编辑
                                </view>
                                <view
                                    class="text-error"
                                    @click="handleDelete(index)"
                                >
                                    删除
                                </view>
                            </view>
                        </uni-td>
                    </uni-tr>
                </uni-table>
                <view class="inline-flex mt-[20rpx]">
                    <u-button
                        type="primary"
                        size="medium"
                        :disabled="formData.menus.length >= 3"
                        @click="handleShowMenuPopup()"
                    >
                        +添加菜单
                    </u-button>
                </view>
            </view>
        </u-form-item>
        <AddMenus
            v-model:show="menuState.show"
            :type="menuState.type"
            v-model:data="menuState.data"
            @confirm="handleConfirm"
        />
    </view>
</template>

<script setup lang="ts">
import { cloneDeep } from 'lodash'
import { useVModel } from '@vueuse/core'
import { reactive, ref } from 'vue'
import { useAppStore } from '@/stores/app'
import AddMenus from './add-menus.vue'
const props = withDefaults(
    defineProps<{
        modelValue: Record<string, any>
    }>(),
    {}
)

const emit = defineEmits<{
    (event: 'update:modelValue', value: Record<string, any>): void
}>()
const formData = useVModel(props, 'modelValue', emit)

const menuState = reactive({
    show: false,
    type: 'add',
    data: {},
    index: 0
})
const handleShowMenuPopup = (
    type = 'add',
    data = {
        images: [],
        content: '',
        keyword: ''
    }
) => {
    menuState.show = true
    menuState.type = type
    menuState.data = cloneDeep(data)
    menuState.index = formData.value.menus?.indexOf(data)
}

const handleDelete = (index: number) => {
    formData.value.menus?.splice(index, 1)
}

const handleConfirm = (data: any) => {
    switch (menuState.type) {
        case 'add':
            if (formData.value.menus?.length < 3) {
                formData.value.menus?.push(data)
            }
            break
        case 'edit':
            if (menuState.index !== -1) {
                formData.value.menus?.splice(menuState.index, 1, data)
            }
            break
    }
}
</script>
