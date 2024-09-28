<template>
    <Popup title="重命名" ref="popRef" async @confirm="submit" @close="$emit('close')">
        <el-form>
            <el-form-item label="文件名称" class="is-required">
                <el-input placeholder="请输入文件名称" v-model="formData.name"></el-input>
            </el-form-item>
        </el-form>
    </Popup>
</template>

<script setup lang="ts">
import {
    fileRename
} from '@/api/my_database'
const popRef = shallowRef()

const emits = defineEmits(['success','close'])

const formData = ref({
    name:'',
    fd_id:-1
})

//提交
const submit = async()=>{
    await fileRename({...formData.value})
    emits('success')
    popRef.value.close()
}

const open = (id:number)=>{
    popRef.value.open()
    formData.value.fd_id = id
    
    
}

defineExpose({open})

</script>

<style scoped lang="scss">

</style>