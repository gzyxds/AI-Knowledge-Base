<template>
    <Popup ref="popupRef" fullscreen center title="修正问答" async @confirm="handleConfirm">
        <ElScrollbar>
            <el-form ref="formRef" :model="formData" :rules="formRules" label-width="120px">
                <el-form-item label="所属应用">
                    <div>{{ formData.name }}</div>
                </el-form-item>
                <el-form-item label="选择知识库" prop="kb_id">
                    <el-select class="w-[240px]" v-model="formData.kb_id">
                        <el-option
                            v-for="(item, index) in optionsData.knowledge"
                            :key="index"
                            :label="`${item.name}`"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="提问问题" prop="ask">
                    <el-input
                        v-model="formData.ask"
                        placeholder="请输入问题"
                        type="textarea"
                        resize="none"
                        :rows="6"
                        maxlength="600"
                        show-word-limit
                        clearable
                    />
                </el-form-item>
                <el-form-item label="问题答案" prop="reply">
                    <el-input
                        v-model="formData.reply"
                        placeholder="请输入答案"
                        type="textarea"
                        resize="none"
                        :rows="20"
                        clearable
                    />
                </el-form-item>
            </el-form>
        </ElScrollbar>
    </Popup>
</template>
<script lang="ts" setup>
// import { knowKnowledgeAll } from '@/api/knowledge_training/manage'
import type Popup from '@/components/popup/index.vue'
import { useDictOptions } from '@/hooks/useDictOptions'
import type { FormInstance, FormRules } from 'element-plus'

const emit = defineEmits<{
    (event: 'confirm', value: any): void
}>()

const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formData = ref({
    name: '',
    kb_id: '',
    ask: '',
    reply: ''
})

const formRules: FormRules = {
    kb_id: [
        {
            required: true,
            message: '选择知识库'
        }
    ],
    ask: [
        {
            required: true,
            message: '请输入问题'
        }
    ],
    reply: [
        {
            required: true,
            message: '请输入答案'
        }
    ]
}
const open = (data: any) => {
    formData.value = {
        ...formData.value,
        ask: data.ask,
        reply: data.reply,
        name: data.name
    }
    popupRef.value?.open()
}
const close = () => {
    popupRef.value?.close()
}
const handleConfirm = async () => {
    await formRef.value?.validate()
    emit('confirm', formData.value)
}

const { optionsData } = useDictOptions<{
    knowledge: any[]
}>({
    knowledge: {
        // api: knowKnowledgeAll,
        api: () => {},
        params: {
            type: 1
        }
    }
})

defineExpose({
    open,
    close
})
</script>
