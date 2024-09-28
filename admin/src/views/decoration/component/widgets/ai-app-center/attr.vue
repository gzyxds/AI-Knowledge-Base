<template>
    <div class="max-w-[400px]">
        <el-form label-width="85px">
            <el-tabs v-model="activeName">
                <el-tab-pane label="应用配置" name="common">
                    <div>
                        <el-button type="primary" @click="handleAdd">添加应用</el-button>
                    </div>
                    <el-scrollbar height="100%" ref="scrollbarRef" class="app-scroll">
                        <Draggable
                            class="draggable"
                            v-model="content.data"
                            animation="300"
                            handle=".drag-move"
                        >
                            <template v-slot:item="{ element, index }">
                                <del-wrap @close="handleDelete(index)" class="max-w-[400px] mr-2 mt-4">
                                    <div class="bg-fill-light w-full p-4 mb-4">
                                        <el-form-item label="应用封面">
                                            <material-picker
                                                v-model="element.image"
                                                upload-class="bg-body"
                                                exclude-domain
                                                size="100px"
                                            >
                                            </material-picker>
                                        </el-form-item>
                                        <el-form-item label="应用标题">
                                            <el-input
                                                v-model="element.title"
                                                placeholder="请输入应用标题"
                                            />
                                        </el-form-item>
                                        <el-form-item label="应用描述">
                                            <el-input v-model="element.desc" placeholder="请输入应用描述"/>
                                        </el-form-item>
                                        <el-form-item label="pc链接">
                                            <link-picker type="pc" v-model="element.pcLink"/>
                                        </el-form-item>
                                        <el-form-item label="移动端链接">
                                            <link-picker type="mobile" v-model="element.link"/>
                                        </el-form-item>
                                        <el-form-item label="是否显示">
                                            <div class="flex-1 flex items-center">
                                                <el-switch
                                                    v-model="element.is_show"
                                                    active-value="1"
                                                    inactive-value="0"
                                                />
                                                <div class="drag-move cursor-move ml-auto">
                                                    <icon name="el-icon-Rank" size="18"/>
                                                </div>
                                            </div>
                                        </el-form-item>
                                    </div>
                                </del-wrap>
                            </template>
                        </Draggable>
                    </el-scrollbar>
                </el-tab-pane>
                <el-tab-pane label="PC配置" name="pc">
                    <el-form-item label="背景图片">
                        <div>
                            <material-picker
                                v-model="content.pc_background"
                                upload-class="bg-body"
                                exclude-domain
                            />
                            <div class="form-tips">
                                建议图片尺寸为：1920*300
                            </div>
                        </div>
                    </el-form-item>
                    <el-form-item label="标题名称">
                        <el-input v-model="content.pc_title" />
                    </el-form-item>
                    <el-form-item label="按钮显示">
                        <el-radio-group v-model="content.pc_text_color">
                            <el-radio :label="1">黑色</el-radio>
                            <el-radio :label="2">白色</el-radio>
                            <el-radio :label="3">主题色</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <div class="form-tips !text-[14px]">
                        PC端配置对应PC应用中心的头部装修设置。
                        <el-popover placement="bottom-start" width="auto" trigger="hover">
                            <template #reference>
                                <el-button link type="primary"> 查看示例 </el-button>
                            </template>

                            <img :src="PcExample" alt="" class="w-[600px]" />
                        </el-popover>
                    </div>
                </el-tab-pane>
                <el-tab-pane label="移动端配置" name="mobile_title">
                    <el-form-item label="标题名称">
                        <el-input v-model="content.mobile_title" />
                    </el-form-item>
                    <div class="form-tips !text-[14px]">
                        移动端配置对应移动端应用中心的头部装修设置。
                        <el-popover placement="bottom-start" width="auto" trigger="hover">
                            <template #reference>
                                <el-button link type="primary"> 查看示例 </el-button>
                            </template>

                            <img :src="MobileExample" alt="" class="w-[300px]" />
                        </el-popover>
                    </div>
                </el-tab-pane>
            </el-tabs>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type {PropType} from 'vue'
import type options from './options'
import Draggable from 'vuedraggable'

import PcExample from '../../../image/pc_example_app.png'
import MobileExample from '../../../image/mobile_example_app.png'

type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})

const activeName = ref('common')
const scrollbarRef = ref(null);

const handleAdd = async () => {
    props.content.data.push({
        image: '',
        title: '',
        desc: '',
        pcLink: {},
        link: {},
        is_show: '1'
    } as never)
    await nextTick()
    if (scrollbarRef.value) {
        scrollbarRef.value.scrollTo({ top: 999999 });
    }
}
const handleDelete = (index: number) => {
    props.content.data.splice(index, 1)
}
</script>

<style lang="scss" scoped>
.app-scroll {
    height: calc(100vh - 350px);
}
</style>
