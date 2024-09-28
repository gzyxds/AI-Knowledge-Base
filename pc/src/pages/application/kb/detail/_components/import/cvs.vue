<template>
    <div>
        <div class="h-full flex flex-col">
            <div v-loading="loading" class="py-[16px]">
                <el-upload
                    ref="uploadRef"
                    drag
                    :on-change="fileInput"
                    :auto-upload="false"
                    :show-file-list="false"
                    :accept="accept"
                    :multiple="true"
                    :limit="50"
                >
                    <div class="el-upload__text">
                        <Icon name="el-icon-Upload" />
                        拖拽文件至此，或点击<em> 选择文件 </em>
                    </div>
                    <div class="el-upload__text">支持 {{ accept }} 文件</div>
                    <div class="el-upload__text">
                        <el-button
                            type="primary"
                            class="ml-2"
                            link
                            @click.stop=""
                        >
                            <a href="/static/xlsxTemplate.xlsx" target="_blank"
                                >点击下载模版</a
                            >
                        </el-button>
                    </div>

                    <template #tip>
                        <div class="el-upload__tip">
                            先完成填写后再上传，问题总数建议不要超过1000条，否则上传会卡顿
                        </div>
                        <div class="el-upload__tip">
                            导入前会进行去重，如果问题和答案完全相同，则不会被导入，所以最终导入的内容可能会比文件的内容少。但是，对于带有换行的内容，目前无法去重。
                        </div>
                    </template>
                </el-upload>
            </div>
            <!-- <div class="flex-1 min-h-0">
        <el-scrollbar>
          <div v-if="fileList.length == 0" class="p-4 bg-page rounded">
            <div>例：</div>
            <div v-for="(item, index) in tableData" :key="index">
              <div>Q{{ index + 1 }}.{{ item.question }}</div>
              <div>A{{ index + 1 }}.{{ item.answer }}</div>
            </div>
          </div>
          <div v-for="(file, fIndex) in fileList" :key="fIndex">
            <div class="my-2 text-tx-primary font-medium text-lg">
              #{{ fIndex + 1 }}
              {{ file.name }}
              <Icon
                class="icon-delete align-[-3px] cursor-pointer"
                name="el-icon-Delete"
                @click="handleDelete(file)"
              />
            </div>
            <div class="p-4 bg-page rounded">
              <div v-for="(item, index) in file.data" :key="index">
                <div>Q{{ index + 1 }}.{{ item.q }}</div>
                <div>A{{ index + 1 }}.{{ item.a }}</div>
              </div>
            </div>
          </div>
        </el-scrollbar>
      </div> -->
            <div
                v-if="data.length > 0"
                class="grid grid-cols-2 gap-4 flex-1 min-h-[500px]"
            >
                <div style="border-right: 1px solid #eeeeee">
                    <div class="mt-4 max-w-[500px]">
                        <div
                            v-for="(item, index) in data"
                            :key="index"
                            class="fileItem flex items-center p-2 rounded-lg mt-1 hover:cursor-pointer hover:bg-page transition duration-300"
                            :class="{ 'bg-page': showIndex == index }"
                            @click="selectShow(index)"
                        >
                            <Icon
                                name="el-icon-Folder"
                                :size="16"
                                color="#ffc94d"
                            />
                            <div class="ml-2">
                                {{ item.name }}
                            </div>
                            <div
                                class="closeIcon ml-auto opacity-0 transition duration-300 flex items-center"
                            >
                                <Icon
                                    name="el-icon-DeleteFilled"
                                    @click="delFile(index)"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="text-lg">
                        分段预览（{{ data[showIndex]?.data.length }}组）
                    </div>
                    <div class="flex-auto mt-2 h-[100px]">
                        <el-scrollbar height="100%">
                            <div
                                class="bg-page rounded p-[10px] mt-2"
                                v-for="(item, index) in data[showIndex]?.data"
                                :key="index"
                            >
                                <div class="flex items-center">
                                    <span class="bg-white px-4 rounded">
                                        #{{ index + 1 }}
                                    </span>
                                    <span
                                        class="mx-2 text-[#000] flex-1 line-clamp-1"
                                    >
                                        {{ data[showIndex].name }}
                                    </span>
                                    <Icon
                                        class="icon-delete text-primary cursor-pointer"
                                        name="el-icon-Delete"
                                        @click="handleDelete(index)"
                                    />
                                </div>
                                <div class="mt-2">
                                    <div class="whitespace-pre-line">
                                        q:{{ item.q }}
                                    </div>
                                    <div class="whitespace-pre-line">
                                        a:{{ item.a }}
                                    </div>
                                </div>
                            </div>
                        </el-scrollbar>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { UploadFile, UploadInstance } from 'element-plus'
import { useVModel } from '@vueuse/core'
import { isArray } from 'lodash-es'
import { type IDataItem, isSameFile } from './hook'

const props = defineProps<{
    modelValue: IDataItem[]
}>()
const emit = defineEmits(['update:modelValue'])
const data = useVModel(props, 'modelValue', emit)

const fileAccept = ['.csv', '.xlsx']
const accept = fileAccept.join(', ')
const fileList = ref<File[]>([])
const uploadRef = shallowRef<UploadInstance>()

//加载
const loading = ref(false)
//预览
const showIndex = ref(-1)

const tableData = ref([
    {
        question: '什么是知识库',
        answer: '知识库是指一个集中存储和管理知识的系统或平...'
    },
    {
        question: '如何创建知识库',
        answer: '1.确定目标和范围：明确知识库的目标和用途，确定...'
    }
])

//选择文件
const fileInput = async ({ raw: file }: UploadFile) => {
    try {
        if (file) {
            loading.value = true
            await isSameFile(file, fileList.value)
            const content = await parseFile(file)

            if (!content || !content.length) {
                throw '解析结果为空，已自动忽略'
            }
            if (!isArray(content)) {
                throw '解析失败'
            }
            //@ts-ignore
            file.data = content

            data.value.push({
                name: file.name,
                path: '',
                data: content
            })
            fileList.value.push(file)
            console.log(fileList.value[0].name)
            selectShow(fileList.value.length - 1)
        }
    } catch (error: any) {
        feedback.msgError(error)
    } finally {
        loading.value = false
        uploadRef.value?.clearFiles()
    }
}

const handleDelete = async (index: any) => {
    data.value[showIndex.value].data.splice(index, 1)
}

//选择文件
const parseFile = async (file: any) => {
    const suffix = file.name.substring(file.name.lastIndexOf('.') + 1)
    let res = ''
    switch (suffix) {
        case 'csv':
            res = await readCsvContent(file)
            break
        case 'xlsx':
            res = await readXlsxContent(file)
            break
    }
    return res
}

//选择预览文件
const selectShow = (index: number) => {
    // console.log(index)
    showIndex.value = index
    //   showContent.length = 0
    //   splitContent(data.value[index].question)
}
//删除文件
const delFile = (index: number) => {
    data.value.splice(index, 1)
    fileList.value.splice(index, 1)
}

// watch(
//   fileList,
//   (fileList) => {
//     data.value = fileList.reduce((prev: IDataItem[], file: any) => {
//       const processingData = file.data.map((item: any) => ({
//         ...item,
//         source: file.name
//       }))
//       return [...prev, ...processingData]
//     }, [])
//   },
//   {
//     deep: true
//   }
// )
defineExpose({
    clearFiles: () => {
        fileList.value = []
    }
})
</script>

<style scoped lang="scss">
.fileItem {
    &:hover {
        .closeIcon {
            opacity: 1 !important;
        }
    }
}
</style>
