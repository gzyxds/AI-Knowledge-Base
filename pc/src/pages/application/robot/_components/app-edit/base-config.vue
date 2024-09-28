<template>
    <div class="pt-[10px]">
        <el-form-item label="智能体图标" prop="image">
            <div>
                <div>
                    <UploadImg v-model="formData.image" />
                </div>
                <div class="form-tips">建议尺寸：240*240px</div>
            </div>
        </el-form-item>
        <el-form-item label="智能体名称" prop="name">
            <div class="w-80">
                <el-input
                    v-model="formData.name"
                    placeholder="请输入智能体名称"
                    clearable
                />
            </div>
        </el-form-item>
        <el-form-item label="简介" prop="intro">
            <div class="w-80">
                <el-input
                    v-model="formData.intro"
                    placeholder="请简单描述下给你的智能体"
                    type="textarea"
                    :autosize="{ minRows: 3, maxRows: 6 }"
                    :maxlength="200"
                    show-word-limit
                    clearable
                />
            </div>
        </el-form-item>
        <el-form-item label="关联知识库" prop="kb_ids">
            <div>
                <div class="flex">
                    <div class="w-80">
                        <el-select
                            v-model="formData.kb_ids"
                            placeholder="请选择关联知识库"
                            clearable
                            multiple
                        >
                            <el-option
                                v-for="item in optionsData.knowledge"
                                :key="item.id"
                                :label="`${item.name}`"
                                :value="String(item.id)"
                            />
                        </el-select>
                    </div>

                    <div class="ml-2 flex items-center">
                        <el-button type="primary" link @click="addKb">
                            新增知识库
                        </el-button>
                        <span class="px-1">|</span>
                        <el-button type="primary" link @click="refresh">
                            刷新
                        </el-button>
                    </div>
                </div>
                <div class="form-tips">需选择同一种训练模型的知识库</div>
            </div>
        </el-form-item>
        <el-form-item label="角色设定" prop="roles_prompt">
            <div class="flex-1 min-w-0">
                <div class="flex">
                    <div class="w-80">
                        <el-input
                            v-model="formData.roles_prompt"
                            placeholder="请输入角色设定"
                            type="textarea"
                            :autosize="{ minRows: 4, maxRows: 6 }"
                            clearable
                        />
                    </div>
                </div>
                <div class="form-tips">
                    引导应用的聊天方向，该内容会被固定在上下文的开头。
                </div>
            </div>
        </el-form-item>
        <el-form-item label="对话图标">
            <div>
                <!-- <material-picker
          v-model="formData.chat_icon"
          :exclude-domain="true"
        /> -->
                <UploadImg
                    v-model="formData.icons"
                    :exclude-domain="false"
                    :can-close="true"
                />
                <div class="form-tips">
                    不设置的话，对话图标默认拿智能体封面
                </div>
            </div>
        </el-form-item>
        <el-form-item label="对话上下文" prop="is_show_context">
            <div>
                <el-radio-group v-model="formData.is_show_context">
                    <el-radio :label="1"> 显示 </el-radio>
                    <el-radio :label="0"> 关闭 </el-radio>
                </el-radio-group>
                <div class="form-tips">在前台显示对话上下文，默认显示</div>
            </div>
        </el-form-item>
        <el-form-item label="引用内容" prop="is_show_quote">
            <div>
                <el-radio-group v-model="formData.is_show_quote">
                    <el-radio :label="1"> 显示 </el-radio>
                    <el-radio :label="0"> 关闭 </el-radio>
                </el-radio-group>
                <div class="form-tips">在前台显示引用内容，默认显示</div>
            </div>
        </el-form-item>
        <el-form-item label="问答相似问题推荐" prop="related_issues_num" required>
            <div>
                <div class="flex w-[400px]">
                    <el-slider v-model="formData.related_issues_num" :min="0" :max="10" />
                    <el-input-number v-model="formData.related_issues_num" :min="0" :max="10" class="ml-4 w-[180px]" />
                </div>
                <div class="form-tips">作用于智能体对话问题推荐，填0对话问题推荐不生效</div>
            </div>
        </el-form-item>

        <!--        <el-form-item label="公开智能体" prop="is_public">-->
<!--            <div>-->
<!--                <el-switch-->
<!--                    v-model="formData.is_public"-->
<!--                    inline-prompt-->
<!--                    :active-value="1"-->
<!--                    :inactive-value="0"-->
<!--                    active-text="公开"-->
<!--                    inactive-text="私有"-->
<!--                />-->
<!--                <div class="form-tips">公开智能体后，其他用户都会看到</div>-->
<!--            </div>-->
<!--        </el-form-item>-->
<!--        <el-form-item v-if="formData.is_public" label="选择分类" prop="cate_id">-->
<!--            <div class="w-80">-->
<!--                <el-select-->
<!--                    v-model="formData.cate_id"-->
<!--                    placeholder="请选择分类"-->
<!--                    clearable-->
<!--                >-->
<!--                    <el-option label="全部" :value="0" />-->
<!--                    <el-option-->
<!--                        v-for="item in optionsData.robotCategory"-->
<!--                        :key="item.id"-->
<!--                        :label="item.name"-->
<!--                        :value="item.id"-->
<!--                    />-->
<!--                </el-select>-->
<!--                <div class="form-tips">给公开的智能体分类</div>-->
<!--            </div>-->
<!--        </el-form-item>-->
        <addKbPop v-if="popShow" ref="addkbPopRef" @success="refresh" />
    </div>
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { knowKnowledgeList } from '@/api/my_database'
import addKbPop from '../../../kb/_components/addPop.vue'
import { getRobotCategory } from '@/api/robot'

//弹框ref
const addkbPopRef = shallowRef()
const popShow = ref(false)

const props = defineProps<{
    modelValue: Record<string, any>
}>()

const emit = defineEmits<{
    (event: 'update:modelValue', value: Record<string, any>): void
}>()
const formData = useVModel(props, 'modelValue', emit)

const { optionsData, refresh } = useDictOptions<{
    knowledge: any[]
    robotCategory: any[]
}>({
    knowledge: {
        api: knowKnowledgeList,
        params: {
            page_type: 0
        },
        transformData(data) {
            return data.lists || []
        }
    },
    robotCategory: {
        api: getRobotCategory
    }
})

//新增知识库
const addKb = async () => {
    popShow.value = true
    await nextTick()
    addkbPopRef.value.open()
}
</script>
