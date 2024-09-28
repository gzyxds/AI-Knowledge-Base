<template>
    <view>
        <u-form-item label="智能体图标" prop="image" required>
            <view class="mx-[-10rpx] flex-1 min-w-0">
                <app-upload v-model="formData.image" />
            </view>
        </u-form-item>
        <u-form-item label="智能体名称" prop="name" required>
            <view class="flex-1 min-w-0">
                <u-input
                    v-model="formData.name"
                    placeholder="给你的智能体取个响亮的名字"
                    :border="true"
                />
            </view>
        </u-form-item>
        <u-form-item label="智能体简介" prop="intro">
            <view class="flex-1 min-w-0">
                <u-input
                    v-model="formData.intro"
                    type="textarea"
                    :height="150"
                    placeholder="请简单描述下给你的智能体"
                    :border="true"
                />
            </view>
        </u-form-item>
        <u-form-item label="关联知识库" prop="kb_ids">
            <view class="flex-1 min-w-0">
                <app-select
                    v-model="formData.kb_ids"
                    popupTitle="关联知识库选择"
                    :dataLists="kbLists"
                    placeholder="请选择"
                    value="id"
                    type="checkbox"
                >
                    <template #label="{ item }">
                        {{ item.name }}
                    </template>
                </app-select>
            </view>
        </u-form-item>
        <u-form-item label="角色设定" prop="roles_prompt">
            <view class="flex-1">
                <u-input
                    v-model="formData.roles_prompt"
                    type="textarea"
                    :height="200"
                    placeholder="请输入角色设定"
                    :border="true"
                />
                <view class="text-muted">
                    引导应用的聊天方向，该内容会被固定在上下文的开头。
                </view>
            </view>
        </u-form-item>
        <u-form-item label="对话图标" prop="icons" required>
            <view class="mx-[-10rpx] flex-1 min-w-0">
                <app-upload v-model="formData.icons" />
                <view class="text-muted mx-[10rpx]">
                    不设置的话，对话图标默认拿智能体封面
                </view>
            </view>
        </u-form-item>
        <u-form-item
            label="对话上下文"
            label-position="left"
            label-width="150"
            prop="is_show_context"
        >
            <view class="flex-1">
                <view class="flex justify-end items-center">
                    <u-switch
                        v-model="formData.is_show_context"
                        :active-value="1"
                        :inactive-value="0"
                        :size="40"
                    ></u-switch>
                    <view class="ml-[12rpx]">
                        {{ formData.is_show_context == 1 ? '显示' : '隐藏' }}
                    </view>
                </view>
            </view>
        </u-form-item>

        <view class="text-muted">在前台显示对话上下文，默认显示</view>
        <u-form-item
            label="引用内容"
            label-position="left"
            label-width="150"
            prop="is_show_quote"
        >
            <view class="flex-1">
                <view class="flex justify-end items-center">
                    <u-switch
                        v-model="formData.is_show_quote"
                        :active-value="1"
                        :inactive-value="0"
                        :size="40"
                    ></u-switch>
                    <view class="ml-[12rpx]">
                        {{ formData.is_show_quote == 1 ? '显示' : '隐藏' }}
                    </view>
                </view>
            </view>
        </u-form-item>
        <view class="text-muted">在前台显示引用内容，默认显示</view>

        <u-form-item label="问答相似问题推荐" prop="related_issues_num">
            <view class="flex-1 min-w-0">
                <u-input
                    v-model="formData.related_issues_num"
                    placeholder="给你的智能体取个响亮的名字"
                    :border="true"
                />
            </view>
        </u-form-item>
        <view class="text-muted">作用于智能体对话问题推荐，填0对话问题推荐不生效</view>
<!--        <u-form-item-->
<!--            label="公开智能体"-->
<!--            label-position="left"-->
<!--            label-width="150"-->
<!--            prop="is_public"-->
<!--        >-->
<!--            <view class="flex-1">-->
<!--                <view class="flex justify-end items-center">-->
<!--                    <u-switch-->
<!--                        v-model="formData.is_public"-->
<!--                        :active-value="1"-->
<!--                        :inactive-value="0"-->
<!--                        :size="40"-->
<!--                    ></u-switch>-->
<!--                    <view class="ml-[12rpx]">-->
<!--                        {{ formData.is_public == 1 ? '公开' : '私有' }}-->
<!--                    </view>-->
<!--                </view>-->
<!--            </view>-->
<!--        </u-form-item>-->
<!--        <view class="text-muted">公开智能体后，其他用户都会看到</view>-->
<!--        <u-form-item-->
<!--            v-if="formData.is_public == 1"-->
<!--            label="智能体分类"-->
<!--            label-width="150"-->
<!--            prop="cate_id"-->
<!--            required-->
<!--        >-->
<!--            <view class="flex-1">-->
<!--                <app-select-->
<!--                    v-model="formData.cate_id"-->
<!--                    popupTitle="请选择智能体分类"-->
<!--                    :dataLists="robotCategory"-->
<!--                    placeholder="请选择"-->
<!--                    value="id"-->
<!--                    type="radio"-->
<!--                >-->
<!--                </app-select>-->
<!--                <view class="text-muted">给公开的智能体分类</view>-->
<!--            </view>-->
<!--        </u-form-item>-->
    </view>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { getKBList } from '@/api/kb'
import { ref } from 'vue'
import { getRobotCategory } from '@/api/robot'
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
const kbLists = ref([])
const robotCategory = ref<any[]>([])

const getRobotCategoryData = async () => {
    robotCategory.value = await getRobotCategory()
    robotCategory.value.unshift({
        id: 0,
        name: '全部'
    })
}
const getKbData = async () => {
    const { lists = [] } = await getKBList({
        page_type: 0
    })
    kbLists.value = lists
}
getKbData()
getRobotCategoryData()
</script>
