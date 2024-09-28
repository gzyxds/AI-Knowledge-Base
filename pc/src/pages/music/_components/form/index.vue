<template>
    <div
        class="h-full py-[16px] bg-body w-[350px] rounded-[12px] flex flex-col"
    >
        <div class="px-[16px]">
            <div class="bg-page p-[8px] flex rounded-[10px]">
                <div
                    class="flex-1 flex items-center justify-center cursor-pointer py-[7px] text-tx-regular rounded-[10px]"
                    v-for="item in musicModeList"
                    :key="item.mode"
                    :class="{
                        '!bg-primary !text-white':
                            formData.custom_mode === item.mode
                    }"
                    @click="formData.custom_mode = item.mode"
                >
                    <span class="mr-[5px]">
                        {{ item.name }}
                    </span>

                    <el-tooltip
                        :content="item.tips"
                        placement="bottom"
                        effect="light"
                        :teleported="true"
                    >
                        <span class="flex">
                            <Icon name="el-icon-QuestionFilled" />
                        </span>
                    </el-tooltip>
                </div>
            </div>
        </div>
        <div class="flex-1 min-h-0">
            <ElScrollbar>
                <div class="p-[16px]">
                    <el-form
                        ref="formRef"
                        :model="formData"
                        label-position="top"
                        :rules="formRules"
                        :show-message="false"
                    >
                        <el-form-item prop="title">
                            <template #label>
                                <span class="font-bold text-tx-primary"
                                    >歌曲名称</span
                                >
                            </template>
                            <div class="flex-1">
                                <el-input
                                    class="custom-input"
                                    v-model="formData.title"
                                    size="large"
                                    placeholder="给你的歌曲起个好听的名字"
                                    @focus="onFocus"
                                />
                            </div>
                        </el-form-item>
                        <el-form-item prop="prompt">
                            <template #label>
                                <div class="inline-flex w-[96%]">
                                    <div
                                        class="flex-1 mr-auto flex items-center"
                                    >
                                        <span
                                            class="font-bold text-tx-primary mr-[8px]"
                                        >
                                            {{
                                                isMuseMode ? '歌曲描述' : '歌词'
                                            }}
                                        </span>
                                        <el-popover
                                            v-if="!isMuseMode"
                                            placement="right"
                                            :show-arrow="false"
                                            transition="custom-popover"
                                            :width="200"
                                            trigger="hover"
                                            content="建议歌词长度100-1250，太长或太短容易生成失败"
                                        >
                                            <template #reference>
                                                <div
                                                    class="flex items-center cursor-pointer text-[#999999]"
                                                >
                                                    <Icon
                                                        name="el-icon-QuestionFilled"
                                                        :size="14"
                                                    />
                                                </div>
                                            </template>
                                        </el-popover>
                                    </div>
                                    <ElButton
                                        link
                                        size="small"
                                        @click="formData.prompt = ''"
                                    >
                                        <template #icon>
                                            <Icon
                                                name="el-icon-Delete"
                                                :size="12"
                                            />
                                        </template>
                                        清空
                                    </ElButton>
                                </div>
                            </template>
                            <div class="flex-1">
                                <LTextarea
                                    v-model="formData.prompt"
                                    :placeholder="`请输入${
                                        isMuseMode
                                            ? '灵感，例如写一首关于夏天的歌曲'
                                            : '歌词'
                                    }`"
                                    :contentStyle="{
                                        height: '160px'
                                    }"
                                    showWordLimit
                                    maxlength="1300"
                                    @focus="onFocus"
                                >
                                    <template #length-suffix>
                                        <div class="flex p-[10px]">
                                            <div class="flex-1">
                                                <el-button
                                                    size="small"
                                                    v-if="isShowImagine"
                                                    type="primary"
                                                    plain
                                                    style="
                                                        border: none;
                                                        --el-button-hover-bg-color: var(
                                                            --el-button-bg-color
                                                        );
                                                        --el-button-hover-text-color: var(
                                                            --el-button-text-color
                                                        );
                                                    "
                                                    :loading="isLock"
                                                    @click="handelImagine"
                                                >
                                                    <template #icon>
                                                        <Icon
                                                            name="local-icon-bulb"
                                                            :size="12"
                                                        />
                                                    </template>
                                                    <span> 智能联想 </span>

                                                    <span
                                                        v-if="
                                                            musicConfig.imagine
                                                                .price > 0
                                                        "
                                                        >：消耗{{
                                                            musicConfig.imagine
                                                                .price
                                                        }}{{
                                                            appStore.getTokenUnit
                                                        }}</span
                                                    >
                                                </el-button>
                                            </div>
                                        </div>
                                    </template>
                                </LTextarea>
                            </div>
                        </el-form-item>
                        <el-form-item prop="version">
                            <template #label>
                                <span class="font-bold text-tx-primary">
                                    选择版本
                                </span>
                            </template>

                            <div>
                                <el-button
                                    v-for="(
                                        item, value
                                    ) in currentModel.version"
                                    :key="item"
                                    :class="{
                                        '!text-primary !border-primary':
                                            formData.version === String(value)
                                    }"
                                    @click="formData.version = String(value)"
                                >
                                    {{ item }}
                                </el-button>
                            </div>
                        </el-form-item>
                        <el-form-item v-if="isMuseMode">
                            <template #label>
                                <span class="font-bold text-tx-primary">
                                    纯音乐
                                </span>
                            </template>
                            <div>
                                <el-switch
                                    v-model="formData.make_instrumental"
                                    :active-value="1"
                                    :inactive-value="0"
                                />
                            </div>
                        </el-form-item>
                        <el-form-item v-else>
                            <template #label>
                                <div class="w-full flex items-center">
                                    <span
                                        class="font-bold text-tx-primary flex-1"
                                    >
                                        音乐风格
                                    </span>
                                    <ElButton
                                        link
                                        type="primary"
                                        @click="changeStyleCustom"
                                    >
                                        {{
                                            formData.is_style_custom
                                                ? '系统风格'
                                                : '自定义风格'
                                        }}
                                    </ElButton>
                                </div>
                            </template>
                            <!-- <swiper
                               
                                class="w-full h-[290px]"
                                :navigation="true"
                                :modules="[Navigation]"
                            >
                                <swiper-slide
                                    v-for="(i, index) in Math.ceil(
                                        musicConfig.style.length / sliceNum
                                    )"
                                    :key="index"
                                    class="w-full h-full"
                                > -->
                            <div
                                class="flex-1 min-w-0 flex flex-wrap mx-[-6px]"
                                v-if="!formData.is_style_custom"
                            >
                                <div
                                    v-for="item in musicConfig.style"
                                    class="w-[25%] px-[6px]"
                                    :key="item.id"
                                >
                                    <div
                                        class="h-full cursor-pointer"
                                        @click="selectStyle(item.id)"
                                    >
                                        <div
                                            class="pt-[100%] relative h-0 rounded-[12px] overflow-hidden"
                                        >
                                            <div
                                                class="absolute inset-0 left-0 top-0"
                                            >
                                                <el-image
                                                    :src="item.image"
                                                    class="h-full w-full"
                                                />
                                            </div>
                                            <div
                                                v-if="
                                                    formData.style_id.includes(
                                                        item.id
                                                    )
                                                "
                                                class="absolute bg-[var(--el-overlay-color-lighter)] inset-0 left-0 top-0 flex items-center justify-center text-white"
                                            >
                                                <Icon
                                                    name="el-icon-SuccessFilled"
                                                    :size="20"
                                                />
                                            </div>
                                        </div>
                                        <div class="text-center text-xs">
                                            {{ item.name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- </swiper-slide>
                            </swiper> -->

                            <LTextarea
                                v-else
                                v-model="formData.style_custom"
                                placeholder="请输入自定义风格，多个风格以,隔开"
                                :contentStyle="{
                                    height: '120px'
                                }"
                                @focus="onFocus"
                            >
                            </LTextarea>
                        </el-form-item>
                    </el-form>
                </div>
            </ElScrollbar>
        </div>
        <div class="px-[16px] pt-[16px]">
            <el-button
                size="large"
                class="w-full"
                type="primary"
                :loading="isLockGenerate"
                @click="handelMusicGenerate"
            >
                <div>
                    <span class="text-base font-bold">立即生成</span>
                    <span class="text-sm ml-[4px]" v-if="musicConfig.is_member"
                        >会员免费</span
                    >
                    <span
                        class="text-sm ml-[4px] font-normal"
                        v-else-if="currentModel.price > 0"
                    >
                        消耗 {{ currentModel.price }}
                        {{ appStore.getTokenUnit }}
                    </span>
                </div>
            </el-button>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    getMusicConfig,
    postMusicImagine,
    postMusicGenerate
} from '@/api/music'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'
import { Navigation } from 'swiper/modules'
const emit = defineEmits<{
    update: []
}>()

const appStore = useAppStore()
const userStore = useUserStore()
enum MusicMode {
    MUSE = 0,
    LYRIC = 1
}

const musicModeList = ref([
    {
        mode: MusicMode.MUSE,
        name: '灵感模式',
        tips: '只需输入一段歌曲描述，即可生成一首歌'
    },
    {
        mode: MusicMode.LYRIC,
        name: '歌词模式',
        tips: '输入自己的歌词，即可生成一首歌'
    }
])
const formRef = shallowRef()

const formData = reactive({
    channel: 'go_api',
    custom_mode: MusicMode.MUSE,
    title: '',
    prompt: '',
    style_id: [] as number[],
    is_style_custom: 0,
    make_instrumental: 0,
    style_custom: '',
    version: ''
})

const changeStyleCustom = () => {
    formData.is_style_custom = formData.is_style_custom ? 0 : 1
}

const sliceNum = ref(8)
const sliceStyle = (index: number) => {
    return musicConfig.value.style.slice(
        sliceNum.value * index,
        sliceNum.value * (index + 1)
    )
}

const selectStyle = (id: number) => {
    const index = formData.style_id.findIndex((item: any) => item === id)
    if (index !== -1) {
        formData.style_id.splice(index, 1)
    } else {
        formData.style_id.push(id)
    }
}

const isMuseMode = computed(() => formData.custom_mode === MusicMode.MUSE)
const formRules = computed(() => ({
    title: [
        {
            required: true,
            message: '请输入歌曲名称'
        }
    ],
    prompt: [
        {
            required: true,
            message: `请输入${isMuseMode.value ? '歌曲描述' : '歌词'}`
        }
    ],
    version: [
        {
            required: true,
            message: `请选择版本`
        }
    ]
}))

const onFocus = () => {
    if (!userStore.isLogin) {
        userStore.toggleShowLogin()
    }
}

const { data: musicConfig, refresh } = useAsyncData(() => getMusicConfig(), {
    default() {
        return {
            model: {},
            style: [],
            imagine: {}
        }
    },
    lazy: true
})

watch(musicConfig, (value) => {
    formData.channel = value.channel
    if (!formData.version) {
        formData.version = Object.keys(currentModel.value.version)[0] || ''
    }
})
const isShowImagine = computed(() => {
    return !isMuseMode.value && musicConfig.value.imagine.status
})

const currentModel = computed<any>(() => {
    return musicConfig.value.model[formData.channel] || {}
})

const { lockFn: handelImagine, isLock } = useLockFn(async () => {
    if (!formData.title) return feedback.msgError('请输入歌曲名称')
    const data = await postMusicImagine({
        prompt: formData.title
    })
    userStore.getUser()
    formData.prompt = data.content
})

const { lockFn: handelMusicGenerate, isLock: isLockGenerate } = useLockFn(
    async () => {
        try {
            if (!formData.title) return feedback.msgError('请输入歌曲名称')
            if (!formData.prompt)
                return feedback.msgError(
                    `请输入${!isMuseMode.value ? '歌词' : '歌曲描述'}`
                )
            if (!formData.version) return feedback.msgError('请选择版本')
            await postMusicGenerate({
                ...formData,
                style_id: formData.is_style_custom ? [] : formData.style_id
            })
            formData.prompt = ''
            formData.title = ''
            formData.style_id = []
            formData.style_custom = ''
            userStore.getUser()
            refresh()
            emit('update')
        } catch (error) {
        } finally {
        }
    }
)
</script>

<style lang="scss" scoped>
.custom-input {
    width: 100%;
    :deep() {
        .el-input__wrapper {
            @apply bg-page;
            &:not(.is-focus) {
                box-shadow: none;
            }
        }
    }
}
.swiper {
    --swiper-navigation-size: 12px;
    --swiper-navigation-color: var(--el-bg-color);
    :deep() {
        .swiper-button-prev,
        .swiper-button-next {
            top: auto;
            bottom: 10px;
            width: 28px;
            height: 28px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
        }
        .swiper-button-prev {
            left: 50%;
            transform: translateX(-140%);
        }
        .swiper-button-next {
            right: 50%;
            transform: translateX(140%);
        }
    }
}
</style>
