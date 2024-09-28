<template>
    <div class="h-full">
        <dataList
            class="h-full"
            v-if="showModule == ModuleEnum['dataList']"
            @to-import="showModule = ModuleEnum['fileImport']"
            @to-item-list="toItemList"
            :id="id"
        ></dataList>

        <ElScrollbar v-if="showModule == ModuleEnum['fileImport']">
            <div class="p-main">
                <importData
                    :id="id"
                    @back="
                        () => {
                            showModule = ModuleEnum['dataList']
                        }
                    "
                ></importData>
            </div>
        </ElScrollbar>
        <ElScrollbar v-if="showModule == ModuleEnum['manualImport']">
            <div class="p-main">
                <manualImport
                    @back="
                        () => {
                            showModule = ModuleEnum['dataList']
                        }
                    "
                    :item-id="isSelectItemId"
                    :item-name="isSelectName"
                ></manualImport>
            </div>
        </ElScrollbar>
    </div>
</template>

<script setup lang="ts">
// import dataList from './datalist.vue'
import dataList from './study_com/datalist.vue'
import importData from './study_com/importData.vue'
import manualImport from './study_com/itemList.vue'

const props = defineProps({
    id: {
        type: Number,
        default: 0
    }
})

const isSelectItemId = ref(0)
const isSelectName = ref('')

enum ModuleEnum {
    'dataList' = 1,
    'fileImport' = 2,
    'manualImport' = 3
}

const showModule = ref(ModuleEnum['dataList'])

//跳转至单个文件列表
const toItemList = (id: number, name: string) => {
    isSelectItemId.value = id
    isSelectName.value = name
    showModule.value = ModuleEnum['manualImport']
}

onMounted(() => {
    showModule.value = ModuleEnum['dataList']
    //   setTimeout(() => {
    //     showModule.value = ModuleEnum['manualImport']
    //   }, 500)
})
</script>
