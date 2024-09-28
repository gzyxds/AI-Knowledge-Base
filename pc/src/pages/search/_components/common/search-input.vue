<template>
    <div class="bg-page rounded-[15px] overflow-hidden p-[10px] search-input">
        <div>
            <el-input
                v-model="inputModel"
                :autosize="{ minRows: 2, maxRows: 4 }"
                type="textarea"
                placeholder="输入你想搜索的问题"
                resize="none"
                @keydown="handleInputEnter"
            />
        </div>
        <div class="flex items-center">
            <div class="mr-auto">
                <SearchType v-model:type="typeModel" :model="model">
                    <template #item="{ icon, label }">
                        <div class="flex items-center px-[8px]">
                            <Icon :name="icon" />
                            <span class="px-[6px]">
                                {{ label }}
                            </span>
                            <Icon name="el-icon-ArrowDown" />
                        </div>
                    </template>
                </SearchType>
            </div>
            <div>
                <SearchBtn @click="emit('search')" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useVModels } from '@vueuse/core'
import SearchType from './search-type.vue'
import SearchBtn from './search-btn.vue'
const props = defineProps<{
    mode: 'input' | 'textarea'
    model: string
    type: string
    input: string
}>()
const emit = defineEmits<{
    'update:type': [value: string]
    'update:input': [value: string]
    search: []
}>()

const { type: typeModel, input: inputModel } = useVModels(props, emit)

const handleInputEnter = (e: any) => {
    if (e.shiftKey && e.keyCode === 13) {
        return
    }
    if (e.keyCode === 13) {
        emit('search')
        return e.preventDefault()
    }
}
</script>

<style lang="scss" scoped>
.search-input {
    :deep() {
        .el-textarea__inner {
            box-shadow: none;
            --el-input-bg-color: transparent;
        }
    }
}
</style>
