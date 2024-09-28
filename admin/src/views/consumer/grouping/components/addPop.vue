<template>
    <Popup
        ref="popRef"
        async
        :title="`${id ? '编辑' : '新增'}分组`"
        @confirm="submit"
        @close="$emit('success')"
    >
        <el-form label-width="90px" @submit.prevent="">
            <el-form-item label="分组名称" prop="name" class="is-required">
                <el-input v-model="formData.name" placeholder="请输入分组名称" />
            </el-form-item>
        </el-form>
    </Popup>
</template>

<script setup lang="ts">
import { userGroupingAdd, userGroupingEdit } from '@/api/consumer'

const emits = defineEmits(['success'])
const popRef = shallowRef()

const id = ref(0)

const formData = ref({ name: '' })

const open = (option: { id: number; name: string }) => {
    popRef.value.open()
    if (option.id) {
        id.value = option.id
        formData.value.name = option.name
    }
}

//提交
const submit = async () => {
    if (id.value) {
        await userGroupingEdit({ ...formData.value, id: id.value })
    } else {
        await userGroupingAdd(formData.value)
    }

    // emits('success')
    popRef.value.close()
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
