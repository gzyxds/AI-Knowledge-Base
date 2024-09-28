<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="title" @back="$router.back()" />
        </el-card>

        <el-card class="!border-none mt-4" shadow="never">
            <el-form
                ref="memberFormRef"
                :model="formData"
                label-width="120px"
                :rules="formRules"
            >
                <!-- 选项卡 -->
                <el-tabs v-model="tabsParams.active" class="demo-tabs">
                    <template
                        v-for="(tabsItem, tabsIndex) in tabsParams.TabsEnumMap"
                        :key="tabsIndex"
                    >
                        <el-tab-pane :label="tabsItem.label" :name="tabsItem.type" lazy>
                            <component :is="tabsItem.comp" :modelValue="formData"></component>
                        </el-tab-pane>
                    </template>
                </el-tabs>
            </el-form>
        </el-card>
        <footer-btns v-perms="['member.memberPackage/add', 'member.memberPackage/edit']">
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
</template>


<script setup lang="ts">
import type { MemberRequest } from '@/api/marketing/member_d'
import type { FormInstance, FormRules } from 'element-plus'
import { memberAdd, memberDetail, memberEdit } from '@/api/marketing/member'
import feedback from "@/utils/feedback";

const BaseSettings = markRaw(defineAsyncComponent(() => import('./_components/base-settings.vue')));
const ModelLimits = markRaw(defineAsyncComponent(() => import('./_components/model-limits.vue')));
const AppLimits = markRaw(defineAsyncComponent(() => import('./_components/app-limits.vue')));
const MemberBenefits = markRaw(defineAsyncComponent(() => import('./_components/member-benefits.vue')));

const router = useRouter()
const { query } = useRoute()

const title = computed(() => {
    return query.id ? '编辑会员等级' : '新增会员等级'
})

enum TabsEnum {
    BASE_SETTINGS = 'BaseSettings',
    MODEL_LIMITS = 'ModelLimits',
    APP_LIMITS = 'AppLimits',
    MEMBER_BENEFITS = 'MemberBenefits'
}
const tabsParams = reactive({
    active: TabsEnum.BASE_SETTINGS,
    TabsEnumMap: [
        {
            label: '基础设置',
            comp: markRaw(BaseSettings),
            type: TabsEnum.BASE_SETTINGS
        },
        {
            label: '模型限制',
            comp: markRaw(ModelLimits),
            type: TabsEnum.MODEL_LIMITS
        },
        {
            label: '应用限制',
            comp: markRaw(AppLimits),
            type: TabsEnum.APP_LIMITS
        },
        {
            label: '会员权益',
            comp: markRaw(MemberBenefits),
            type: TabsEnum.MEMBER_BENEFITS
        }
    ]
})

const memberFormRef = shallowRef<FormInstance>()
const oldName = ref<string>('')
const formData = reactive<MemberRequest>({
    id: '',
    name: '',
    status: 1,
    is_recommend: 1,
    describe: '',
    sort: 0,
    is_use: '',
    price_list: [],
    model_list: {
        chat_model: [],
        vector_model: []
    },
    apply_list: [],
    benefits_list: []
})

//表单校验规则
const formRules = reactive<FormRules>({
    name: [
        {
            required: true,
            message: '请输入套餐名称',
            trigger: ['blur']
        }
    ],
    status:  [{ required: true }]
})

//获取套餐详情
const getDetail = async (id: number) => {
    const data = await memberDetail({
        id
    })
    Reflect.ownKeys(formData).map((key) => {
        //@ts-ignore
        formData[key] = data[key]
    })
    oldName.value = data.name
}

//提交
const handleSave = async () => {
    try {
        await memberFormRef.value?.validate()
        if (formData.is_use && oldName.value != formData.name) {
            try {
                await feedback.customConfirm('', '', '你的等级名称已调整，相关联的用户将会跟随调整到最新信息请谨慎操作', { color: 'red' }, '', '')
            } catch (e) {
                return
            }
        }
        query.id ? await memberEdit(formData) : await memberAdd(formData)
        setTimeout(() => { router.back() }, 500)
    } catch (error) {
        console.log('保存会员等级失败=>', error)
    }
}

onMounted(() => {
    query.id && getDetail(Number(query.id))
})
</script>

<style scoped lang="scss"></style>
