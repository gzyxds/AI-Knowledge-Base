<template>
    <div class="pt-[10px]">
        <el-form-item label="欢迎语" prop="welcome_introducer">
            <div class="w-[600px]">
                <el-input
                    v-model="formData.welcome_introducer"
                    placeholder=""
                    type="textarea"
                    :autosize="{ minRows: 8, maxRows: 8 }"
                    clearable
                    resize="none"
                />
                <div class="form-tips">
                    打开聊天窗口后会主动发送，添加双井号可添加提问示例，例如：#帮我写一则关于xxx的文案#
                    <br />
                    多个问题请用回车换行
                </div>
            </div>
        </el-form-item>
        <el-form-item label="底部标识" prop="copyright">
            <div class="w-[600px]">
                <el-input v-model="formData.copyright" placeholder="" clearable />
                <div class="form-tips flex items-center">不填写不显示</div>
            </div>
        </el-form-item>
        <el-form-item label="菜单设置">
            <div class="flex-1 min-w-0">
                <div class="max-w-[600px]">
                    <el-table size="large" :data="formData.menus">
                        <el-table-column label="关键词" prop="keyword" min-width="120" />
                        <el-table-column label="回复内容" min-width="120">
                            <template #default="{ row }">
                                {{ row.images?.length ? '文字+图片' : '文字' }}
                            </template>
                        </el-table-column>

                        <el-table-column label="操作" width="200">
                            <template #default="{ $index, row }">
                                <el-button
                                    type="primary"
                                    :disabled="false"
                                    link
                                    @click="handleShowMenuPopup('view', row)"
                                >
                                    查看
                                </el-button>
                                <el-button
                                    type="primary"
                                    link
                                    @click="handleShowMenuPopup('edit', row)"
                                >
                                    编辑
                                </el-button>
                                <el-button type="danger" link @click="handleDelete($index)">
                                    删除
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
                <div class="mt-4">
                    <el-button
                        type="primary"
                        :disabled="formData.menus.length >= 3"
                        @click="handleShowMenuPopup()"
                    >
                        +添加菜单
                    </el-button>
                </div>
                <div class="form-tips">用户点击菜单后，将回复对应内容。此类消息不消耗余额。</div>
            </div>
        </el-form-item>
        <AddMenu
            v-model:show="menuState.show"
            :type="menuState.type"
            v-model:data="menuState.data"
            @confirm="handleConfirm"
        />
    </div>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import AddMenu from './add-menu.vue'
import { cloneDeep } from 'lodash'
const props = defineProps<{
    modelValue: Record<string, any>
}>()
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

const handleShowMenuPopup = (type = 'add', data = {}) => {
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
