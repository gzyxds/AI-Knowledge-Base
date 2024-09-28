<template>
    <div class="pt-[10px]">
        <el-form-item label="智能体图标" prop="image">
            <div>
                <div>
                    <material-picker v-model="formData.image" :limit="1" />
                </div>
                <div class="form-tips">建议尺寸：240*240px</div>
            </div>
        </el-form-item>
        <el-form-item label="智能体名称" prop="name">
            <div class="w-80">
                <el-input v-model="formData.name" placeholder="请输入智能体名称" clearable />
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
        <el-form-item label="关联知识库">
            <div>
                <div class="flex">
                    <!-- @vue-ignore -->
                    <el-select
                        class="!w-[320px]"
                        :model-value="formData.knows?.map((item) => item.id)"
                        placeholder="请选择关联知识库"
                        clearable
                        multiple
                    >
                        <el-option
                            v-for="item in formData.knows"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </div>
                <div class="form-tips">只支持多选同一种类型的知识库</div>
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
                <div class="form-tips">引导应用的聊天方向，该内容会被固定在上下文的开头。</div>
            </div>
        </el-form-item>
        <el-form-item label="对话图标">
            <div>
                <material-picker disabled v-model="formData.icons" />
                <div class="form-tips">不设置的话，对话图标默认拿智能体封面</div>
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
        <el-form-item v-if="formData.is_public" label="选择分类" prop="cate_id">
            <div class="w-80">
                <el-select v-model="formData.cate_id" placeholder="请选择分类" clearable>
                    <el-option label="全部" :value="0" />
                    <el-option
                        v-for="item in optionsData.robotCategory"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                    />
                </el-select>
                <div class="form-tips">给公开的智能体分类</div>
            </div>
        </el-form-item>
        <!-- <el-form-item label="排序" prop="sort">
            <div>
                <el-input-number v-model="formData.sort" :min="0" :max="9999" />
                <div class="form-tips">默认为0， 数值越大越排前</div>
            </div>
        </el-form-item>
        <el-form-item label="状态" prop="is_enable">
            <el-switch v-model="formData.is_enable" :active-value="1" :inactive-value="0" />
        </el-form-item> -->
    </div>
</template>

<script setup lang="ts">
import { getRobotCateAll } from '@/api/knowledge_base/robot_square'
import { useDictOptions } from '@/hooks/useDictOptions'
import { useVModel } from '@vueuse/core'

const props = defineProps<{
    modelValue: Record<string, any>
}>()
const emit = defineEmits<{
    (event: 'update:modelValue', value: Record<string, any>): void
}>()
const formData = useVModel(props, 'modelValue', emit)

const { optionsData } = useDictOptions<{
    robotCategory: any[]
}>({
    robotCategory: {
        api: getRobotCateAll
    }
})
</script>
