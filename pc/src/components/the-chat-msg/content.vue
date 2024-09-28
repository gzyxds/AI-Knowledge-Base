<template>
    <div class="chat-content">
        <div class="chat-text">
            <div v-if="filesPlugin.length">
                <template v-for="(item, index) in filesPlugin" :key="index">
                    <RecordImage
                        v-if="item.type == 'image'"
                        :url="item.url"
                        :name="item.name"
                    />
                    <RecordFile
                        v-else-if="item.type == 'file'"
                        :url="item.url"
                        :name="item.name"
                    />
                </template>
            </div>
            <Markdown
                v-if="type === 'html'"
                :content="content"
                :line-numbers="lineNumbers"
                :typing="typing"
                style="color: inherit"
                @click-custom-link="chat($event)"
            />
            <div
                v-else-if="type === 'text'"
                class="whitespace-pre-wrap text-lg"
                :class="{
                    'wait-typing': typing
                }"
            >
                {{ content }}
            </div>
            <div v-if="imagesList.length" class="flex flex-wrap mx-[-5px]">
                <ElImage
                    v-for="(item, index) in imagesList"
                    :key="index"
                    :preview-src-list="imagesList"
                    :preview-teleported="true"
                    :infinite="false"
                    :initial-index="index"
                    :hide-on-click-modal="true"
                    class="w-[120px] h-[120px] mx-[5px] mt-[10px]"
                    :src="item"
                    fit="cover"
                />
            </div>
            <div v-if="videos.length" class="flex flex-wrap mx-[-5px]">
                <div class="w-[120px] h-[120px] mx-[5px] mt-[10px]">
                    <MaterialFile
                        file-size="120px"
                        v-for="(item, index) in videos"
                        :key="index"
                        :uri="item.url"
                        type="video"
                    />
                </div>
            </div>
            <div v-if="files?.length" class="mt-[15px]">
                <div
                    v-for="(item, index) in files"
                    :key="index"
                    class="flex mb-[10px] items-center"
                >
                    <img
                        class="w-[18px] h-[14px] mr-2"
                        src="@/assets/image/icon_folder.png"
                    />
                    <div class="line-clamp-1 mr-[10px] text-tx-primary">
                        {{ item.name }}
                    </div>
                    <ElLink :href="item.url" target="_blank" type="primary">
                        下载
                    </ElLink>
                </div>
            </div>
        </div>

        <div
            v-if="
                !typing && (showCopy || showVoice || showQuote || showContext)
            "
            class="mt-[10px]"
        >
            <ElButton v-if="showRewrite" link @click="emit('rewrite')">
                <template #icon>
                    <Icon name="el-icon-RefreshLeft" />
                </template>
                重新回答
            </ElButton>
            <ElButton v-if="showCopy" link @click="copy(content)">
                <template #icon>
                    <Icon name="el-icon-CopyDocument" />
                </template>
                复制
            </ElButton>
            <template v-if="showVoice">
                <ElButton v-if="audioPlaying" link @click="pause">
                    <template #icon>
                        <Icon name="local-icon-audio_voice" />
                    </template>
                    停止
                </ElButton>
                <ElButton
                    v-else
                    link
                    :loading="audioLoading"
                    @click="play(chatBroadcast)"
                >
                    <template #icon>
                        <Icon name="local-icon-audio_voice" />
                    </template>
                    朗读
                </ElButton>
            </template>

            <ElButton v-if="showPoster" link @click="handleDrawPoster">
                <template #icon>
                    <Icon name="el-icon-Picture" />
                </template>
                生成海报
            </ElButton>
            <ElButton
                v-if="quotes.length && showQuote"
                link
                type="primary"
                @click="showQuotePopup = true"
            >
                {{ quotes.length }}条引用
            </ElButton>
            <ElButton
                v-if="context.length && showContext"
                link
                type="primary"
                @click="showContextPopup = true"
            >
                {{ context.length }}条上下文
            </ElButton>
        </div>

        <ContextPopup
            v-if="showContextPopup"
            :context="context"
            @close="showContextPopup = false"
        />
        <QuotePopup
            v-if="showQuotePopup"
            :quotes="quotes"
            @close="showQuotePopup = false"
        />
        <DialogPoster
            v-if="showPosterPopup"
            ref="posterRef" 
            @close="showPosterPopup = false"
        />
    </div>
</template>

<script lang="ts" setup>
import ContextPopup from './context-popup.vue'
import QuotePopup from './quote-popup.vue'
import { useChatStore } from '@/stores/chat'
import { getChatBroadcast } from '@/api/chat'
import RecordImage from './record-image.vue'
import RecordFile from './record-file.vue'
import DialogPoster from './dialog-poster.vue'
const emit = defineEmits<{
    (event: 'click-custom-link', value: string): void
    (event: 'rewrite'): void
}>()

const chatStore = useChatStore()
const props = withDefaults(
    defineProps<{
        type?: 'text' | 'html'
        recordList?: any[]
        content: string
        context?: any[]
        quotes?: any[]
        images?: any[]
        videos?: any[]
        files?: any[]
        filesPlugin?: any[]
        typing?: boolean
        lineNumbers?: boolean
        showRewrite?: boolean
        showCopy?: boolean
        showContext?: boolean
        showQuote?: boolean
        showVoice?: boolean
        showPoster?: boolean
        recordId?: string | number
        index?: number
        recordType?: number
        channel?: string
        userId?: string
    }>(),
    {
        type: 'text',
        lineNumbers: true,
        content: '',
        context: () => [],
        quotes: () => [],
        images: () => [],
        files: () => [],
        videos: () => [],
        filesPlugin: () => [],
        recordList: () => [],
        typing: false,
        showRewrite: false,
        showCopy: false,
        showContext: false,
        showQuote: false,
        showVoice: false,
        showPoster: false,
        recordId: 0,
        index: 0,
        recordType: 1,
        channel: '',
        userId: ''
    }
)
const showContextPopup = ref(false)
const showQuotePopup = ref(false)
const showPosterPopup = ref(false)

const chatBroadcast = async () => {
    const data = await getChatBroadcast(
        {
            records_id: props.recordId,
            content: props.index,
            type: props.recordType
        },
        {
            Authorization: props.channel,
            Identity: props.userId
        }
    )
    return data.file
}

const imagesList = computed(() => props.images.map(({ url }) => url))
const { play, audioPlaying, pause, audioLoading } = useAudioPlay()

const { copy } = useCopy()

const chat = (content: string) => {
    emit('click-custom-link', content)
}
const posterRef = shallowRef()
const handleDrawPoster = async () => {
    const result = props.recordList.filter((item: any) => {
        return item.id == props.recordId
    })
    if (result.length != 2) {
        feedback.msgError('上下文数据不对～')
        return
    }
    showPosterPopup.value = true
    await nextTick()
    posterRef.value.open({
        title: result[0].content,
        content: result[1].content
    })
}
</script>

<style lang="scss" scoped>
@mixin typing {
    display: inline-block;
    content: '';
    width: 5px;
    height: 14px;
    transform: translate(4px, 2px) scaleY(1.3);
    background-color: #1a202c;
    animation: blink 0.6s infinite;
}
.wait-typing::after {
    @include typing;
}

@keyframes blink {
    from,
    to {
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
}
</style>
