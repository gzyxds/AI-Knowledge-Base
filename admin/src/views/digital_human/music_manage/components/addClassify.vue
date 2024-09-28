<template>
    <Popup
        ref="popRef"
        :title="formData.id ? '编辑分类' : '新增分类'"
        width="500px"
        async
        @confirm="confirm"
        @close="$emit('close')"
    >
        <el-form label-width="90px">
            <el-form-item label="分类名称" class="is-required">
                <el-input placeholder="请输入分类名称" v-model="formData.name" class="w-[320px]" />
            </el-form-item>
            <el-form-item label="排序">
                <div>
                    <el-input class="w-[320px]" v-model="formData.sort" />
                    <div class="form-tips">默认为0，数值越大越排前面</div>
                </div>
            </el-form-item>
            <el-form-item label="状态">
                <div>
                    <el-switch :active-value="1" :inactive-value="0" v-model="formData.status" />
                </div>
            </el-form-item>
        </el-form>
    </Popup>
</template>

<script setup lang="ts">
const popRef = shallowRef()
import { addCategory, editCategory } from '@/api/digital_human/music'

const emits = defineEmits(['success', 'close'])

const formData: any = ref({
    name: '',
    status: 1,
    sort: '0'
})

const open = (row: any) => {
    popRef.value.open()
    Object.keys(formData.value).map((item) => {
        //@ts-ignore
        formData.value[item] = row[item]
    })
    formData.value.id = row.id
}

const confirm = async () => {
    if (formData.value.id) {
        await editCategory({ ...formData.value })
    } else {
        await addCategory({ ...formData.value })
    }

    emits('success')
    popRef.value.close()
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
