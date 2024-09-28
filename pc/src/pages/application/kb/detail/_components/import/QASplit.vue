<template>
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
            </el-upload>
        </div>
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
                                    >{{ data[showIndex].name }}</span
                                >
                                <Icon
                                    class="icon-delete text-primary cursor-pointer"
                                    name="el-icon-Delete"
                                    @click="handleDelete(index)"
                                />
                            </div>
                            <div class="mt-2 whitespace-pre-line">
                                {{ item.q }}
                            </div>
                        </div>
                    </el-scrollbar>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { UploadFile, UploadInstance } from 'element-plus'
import { useVModel } from '@vueuse/core'
import { type IDataItem, isSameFile } from './hook'
import feedback from '@/utils/feedback'
import { splitText2ChunksArray } from '@/utils/textSplitter'
// import { readDocContent, readPdfContent, readTxtContent } from '@/utils/fileReader'

const props = defineProps<{
    modelValue: IDataItem[]
}>()
const emit = defineEmits(['update:modelValue'])
const data = useVModel(props, 'modelValue', emit)

//文件类型
const fileAccept = ['.txt', '.docx', '.pdf', '.md']
const accept = fileAccept.join(', ')
const fileList = ref<File[]>([])
const uploadRef = shallowRef<UploadInstance>()
//加载
const loading = ref(false)

//预览
const showIndex = ref(-1)
const showContent = reactive<string[]>([])
const knowDetail: any = inject('knowDetail')
//选择文件
const fileInput = async ({ raw: file }: UploadFile) => {
    try {
        if (file) {
            loading.value = true
            await isSameFile(file, fileList.value)
            const content = await parseFile(file)
            if (!content) {
                throw '解析结果为空，已自动忽略'
            }
            const isSplitContent: any = splitContent(content)
            data.value.push({
                name: file.name,
                path: '',
                data: isSplitContent
            })

            fileList.value.push(file)
            selectShow(fileList.value.length - 1)
        }
    } catch (error: any) {
        feedback.msgError(error)
    } finally {
        loading.value = false
        uploadRef.value?.clearFiles()
    }
}

const parseFile = async (file: File) => {
    const suffix = file.name.substring(file.name.lastIndexOf('.') + 1)
    let res = ''
    switch (suffix) {
        case 'md':
        case 'txt':
            res = await readTxtContent(file)
            break
        case 'pdf':
            res = await readPdfContent(file)
            break
        case 'doc':
        case 'docx':
            res = await readDocContent(file)
            break
        default:
            res = await readTxtContent(file)
            break
    }
    return res
}

//选择预览文件
const selectShow = (index: number) => {
    // console.log(index)
    showIndex.value = index
    showContent.length = 0
    //   splitContent(data.value[index].question)
}

const splitContent = (content: string) => {
    const data: { q: string; a: string }[] = []
    const contentList = splitText2ChunksArray({
        text: content,
        chunkLen: knowDetail.qa_length
    })
    contentList.forEach((item) => {
        data.push({ q: item, a: '' })
    })
    return data
}
//拆分预览文件
// const splitContent = (content: string) => {
//   const step = 7000
//   const contentList = content.split('')
//   const data = []
//   while (contentList.length) {
//     if (content.length > 7000) {
//       const res = contentList.splice(0, step)
//       const resString = res.join('')
//       //   showContent.push(resString)
//       data.push({ q: resString, a: '' })
//     } else {
//       const resString = contentList.join('')
//       //   showContent.push(resString)
//       data.push({ q: resString, a: '' })
//       break
//     }
//   }
//   return data
// }

const handleDelete = async (index: any) => {
    data.value[showIndex.value].data.splice(index, 1)
}
//删除文件
const delFile = (index: number) => {
    data.value.splice(index, 1)
    fileList.value.splice(index, 1)
}
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
