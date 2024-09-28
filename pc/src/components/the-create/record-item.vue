<template>
    <div
        class="create-record-item"
        :class="{
            '!pt-2': showEdit
        }"
    >
        <!--    预览中    -->
        <div v-show="!showEdit">
            <div class="">
                <div class="mr-auto line-clamp-1 text-lg font-medium">
                    {{ title }}
                </div>
                <el-tag
                    v-if="modelName && appStore.getChatConfig.is_show_model"
                    class="mt-2"
                    type="success"
                    style="--el-tag-border-color: transparent"
                >
                    {{ modelName }}
                </el-tag>
            </div>
            <div
                class="mt-[10px] overflow-hidden"
                :style="{
                    height: autoresize ? '' : 'calc(100vh - 340px)'
                }"
            >
                <Markdown
                    :content="content"
                    :lineClamp="8"
                    :class="{
                        'wait-typing': typing && !content,
                        typing: typing && content
                    }"
                />
            </div>
        </div>
        <client-only>
            <!--    编辑中    -->
            <RecordEditor
                v-if="showEdit"
                :class="{
                    'record-editor': !autoresize
                }"
                :autoresize="autoresize"
                :model-value="content"
                ref="recordEditorRef"
                @content="(c) => emit('content', c)"
            />
        </client-only>

        <div class="flex items-center flex-none mt-[10px]">
            <div class="text-xs text-tx-secondary mr-auto">
                {{ time }}
            </div>
            <template v-if="showVoice">
                <ElButton
                    v-if="audioPlaying"
                    round
                    size="small"
                    color="#fff"
                    @click.stop="pause"
                >
                    <template #icon>
                        <Icon name="local-icon-audio_voice" />
                    </template>
                    停止
                </ElButton>
                <ElButton
                    v-else
                    round
                    size="small"
                    color="#fff"
                    :loading="audioLoading"
                    @click.stop="play(chatBroadcast)"
                >
                    <template #icon>
                        <Icon name="local-icon-audio_voice" />
                    </template>
                    朗读
                </ElButton>
            </template>
            <ElButton
                v-if="showRewrite"
                round
                size="small"
                color="#fff"
                @click.stop="continuation"
            >
                续写
            </ElButton>
            <ElButton
                v-if="showRewrite"
                round
                size="small"
                color="#fff"
                @click.stop="emit('rewrite')"
            >
                重写
            </ElButton>
            <ElButton
                v-if="showCopy"
                round
                size="small"
                color="#fff"
                @click.stop="emit('copy', content)"
            >
                复制
            </ElButton>
            <ElButton
                v-if="showEdit"
                round
                size="small"
                color="#fff"
                @click.stop="showEdit = false"
            >
                取消
            </ElButton>
            <ElButton
                v-if="showEdit"
                round
                size="small"
                type="primary"
                @click.stop="handleSave"
            >
                保存
            </ElButton>
            <ElButton
                v-if="!showEdit"
                round
                size="small"
                color="#fff"
                @click.stop="showEdit = true"
            >
                编辑
            </ElButton>
            <ElButton
                v-if="!showEdit"
                round
                size="small"
                color="#fff"
                @click.stop="handleDel"
            >
                删除
            </ElButton>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { getChatBroadcast, chatUpdate, deleteChatRecord } from '@/api/chat'
import { useAppStore } from '~/stores/app'
import RecordEditor from './record-editor.vue'

const emit = defineEmits<{
    (event: 'content', value: string): void
    (event: 'copy', text: string): void
    (event: 'rewrite'): void
    (event: 'refresh'): void
}>()

const appStore = useAppStore()
const props = withDefaults(
    defineProps<{
        typing?: boolean
        title?: string
        time?: string
        content?: string
        showCopy?: boolean
        autoresize?: boolean
        showRewrite?: boolean
        overflow?: boolean
        showVoice?: boolean
        recordId?: number | string
        index?: number
        edit?: boolean
        modelName?: string
    }>(),
    {
        typing: false,
        title: '',
        time: '',
        content: '',
        overflow: false,
        showCopy: true,
        autoresize: true,
        showRewrite: false,
        showVoice: false,
        recordId: 0,
        index: 0,
        modelName: ''
    }
)

const recordEditorRef = ref<InstanceType<typeof RecordEditor>>()
const showEdit = ref<boolean>(false)

watch(
    () => props.edit,
    (value) => {
        if (value) {
            showEdit.value = true
        }
    },
    { immediate: true }
)

const continuation = async () => {
    try {
        showEdit.value = true
        await nextTick()
        recordEditorRef.value.continuation()
    } catch (error) {}
}

const handleSave = async () => {
    try {
        await chatUpdate({
            id: props.recordId,
            content: recordEditorRef.value.getContent()
        })
        emit('refresh')
        showEdit.value = false
    } catch (error) {}
}

const handleDel = async () => {
    try {
        await feedback.confirm('确定删除该记录？')
        await deleteChatRecord({
            id: props.recordId
        })
        emit('refresh')
    } catch (error) {}
}

const chatBroadcast = async () => {
    const data = await getChatBroadcast({
        records_id: props.recordId,
        content: props.index
    })
    return data.file
}
const { play, audioPlaying, pause, audioLoading } = useAudioPlay()
</script>
<style lang="scss" scoped>
.create-record-item {
    border-radius: 10px;
    padding: 16px;
    @apply bg-page;
    .record-editor {
        :deep() {
            .tox-tinymce {
                height: calc(100vh - 324px) !important;
            }
        }
    }
    :deep() {
        .tox {
            @apply border-none;
        }

        .tox-edit-area::before {
            border-color: var(--el-color-primary-light-5);
        }

        .tox-tbtn,
        .tox-toolbar__overflow,
        .tox-edit-area__iframe,
        .tox-toolbar__primary,
        .tox-toolbar-overlord,
        .tox-editor-header {
            background: transparent !important;
        }

        .tox-editor-container {
            padding-top: 50px;
            position: relative;
        }

        .tox-editor-header {
            box-shadow: none !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
        }

        .tox-tbtn--bespoke {
            background: #ffffff !important;
        }
        button[data-mce-name='h1'],
        button[data-mce-name='h2'],
        button[data-mce-name='h3'] {
            width: auto !important;
            font-size: 18px !important;
        }

        div[title='custom'] {
            .tox-tbtn--select {
                width: auto !important;
                font-size: 16px !important;
            }
        }
        .tox-toolbar__group {
            padding-left: 2px;
        }
        .tox-edit-area {
            margin-bottom: 4px;
        }
    }
}
</style>