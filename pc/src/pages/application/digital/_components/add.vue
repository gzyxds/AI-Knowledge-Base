<template>
  <Popup
    ref="popRef"
    title="创建形象"
    width="500px"
    async
    @confirm="handelSubmit"
  >
    <template #trigger>
      <slot />
    </template>
    <el-form
      class="p-4"
      ref="formRef"
      :model="formData"
      label-width="100px"
      :rules="formRules"
    >
      <el-form-item label="形象名称" prop="name">
        <div class="w-[420px]">
          <el-input
            v-model="formData.name"
            placeholder="请输入形象名称"
            clearable
          />
        </div>
      </el-form-item>
      <el-form-item label="形象头像" prop="avatar">
        <div>
          <div>
            <UploadImg v-model="formData.avatar" />
          </div>
          <div class="form-tips">建议尺寸：50*50px</div>
        </div>
      </el-form-item>
      <el-form-item label="形象封面" prop="image">
        <div>
          <div>
            <UploadImg v-model="formData.image" />
          </div>
          <div class="form-tips">建议尺寸：280px×187px</div>
        </div>
      </el-form-item>
    </el-form>
  </Popup>
</template>

<script setup lang="ts">
import type { FormInstance, FormRules } from 'element-plus'
import { postDigital } from '@/api/digital'

const emits = defineEmits(['success'])
const router = useRouter()
const popRef = shallowRef()

const formData = ref({
  name: '',
  avatar: '',
  image: ''
})

const formRef = shallowRef<FormInstance>()
const formRules = shallowReactive<FormRules>({
  name: [
    {
      required: true,
      message: '请输入形象名称'
    }
  ],
  avatar: [
    {
      required: true,
      type: 'string',
      message: '请选择形象头像'
    }
  ],
  image: [
    {
      required: true,
      type: 'string',
      message: '请选择形象封面'
    }
  ]
})

const handelSubmit = async () => {
  await formRef.value?.validate()
  const { id } = await postDigital(formData.value)
  popRef.value?.close()
  router.push({
    path: '/application/digital/edit',
    query: {
      id
    }
  })
  emits('success')
}
</script>

<style scoped lang="scss"></style>
