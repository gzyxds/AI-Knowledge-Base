<template>
    <view class="chat-record-item" :style="bg ? `--bg-color:${bg}` : ''">
        <view :class="`chat-record-item__${type}`">
            <view class="flex-none">
                <u-image
                    :src="avatar"
                    :width="60"
                    shape="circle"
                    :height="60"
                />
            </view>
            <view
                class="min-w-0 flex flex-col"
                :class="{ 'justify-end': type == 'right' }"
            >
                <div v-if="time" class="ml-[25rpx] mb-[20rpx] text-muted">
                    {{ time }}
                    <u-tag
                        v-if="modelName && appStore.getChatConfig.is_show_model"
                        class="ml-2"
                        size="mini"
                        :text="modelName"
                        style="
                            --color-success: #67c23a;
                            --color-success-light-3: transparent;
                            --color-success-light-9: #f0f9eb;
                        "
                        type="success"
                    />
                </div>
                <view
                    :class="`chat-record-item__${type}-content`"
                    :style="color ? `--color:${color}` : ''"
                >
                    <view v-if="filesPlugin.length">
                        <template
                            v-for="(item, index) in filesPlugin"
                            :key="index"
                        >
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
                    </view>
                    <view
                        class="mb-[20rpx] flex"
                        :class="{
                            'justify-end': type === 'right'
                        }"
                        v-if="audio"
                    >
                        <audio-play
                            :url="audio"
                            :reverse="type === 'right'"
                            :bg-color="'#fff'"
                        ></audio-play>
                    </view>
                    <view>
                        <template v-if="isArray(content)">
                            <view
                                v-for="(item, index) in content"
                                :key="index"
                                class="mb-[20rpx] last-of-type:mb-0"
                                :class="{
                                    'pt-[20rpx] border-t border-solid border-light border-0':
                                        index > 0
                                }"
                            >
                                <text-item
                                    :is-markdown="isMarkdown"
                                    :content="item"
                                    :loading="loading"
                                    :index="index"
                                    :record-id="recordId"
                                    :show-copy-btn="
                                        showCopyBtn && type === 'left'
                                    "
                                    :type="chatType"
                                    :images="images"
                                    :files="files"
                                    :videos="videos"
                                    :show-voice-btn="
                                        appStore.getIsVoiceOpen && showVoiceBtn
                                    "
                                    :color="color"
                                    :show-poster="showPoster"
                                    @poster="emit('poster')"
                                >
                                    <template #btn>
                                        <slot name="btn" />
                                    </template>
                                </text-item>
                            </view>
                        </template>
                        <template v-else>
                            <text-item
                                :is-markdown="isMarkdown"
                                :content="content"
                                :loading="loading"
                                :index="0"
                                :record-id="recordId"
                                :show-copy-btn="showCopyBtn && type === 'left'"
                                :show-voice-btn="
                                    appStore.getIsVoiceOpen && showVoiceBtn
                                "
                                :color="color"
                                :images="images"
                                :files="files"
                                :videos="videos"
                                :type="chatType"
                                :show-poster="showPoster"
                                @click-link="emit('click-link', $event)"
                                @poster="emit('poster')"
                            >
                                <template #btn>
                                    <slot name="btn" />
                                </template>
                            </text-item>
                        </template>

                        <view
                            class="flex items-center text-muted text-sm mt-[16rpx]"
                            v-if="loading"
                        >
                            <u-loading mode="flower"></u-loading>
                            <view class="ml-[10rpx]">加载中，请稍等</view>
                        </view>
                    </view>
                </view>

                <view
                    v-if="type == 'right' && showCopyBtn"
                    class="flex items-center justify-end pr-[20rpx] pt-[10rpx]"
                    @click="copy(content)"
                >
                    <image
                        class="w-[26rpx] h-[26rpx]"
                        src="@/static/images/icon/icon_copy.png"
                    ></image>
                    <text class="text-xs text-muted ml-[8rpx]">复制</text>
                </view>
                <slot name="footer"></slot>

                <!--  生成海报  -->
            </view>
        </view>
        <slot v-if="type == 'left'" name="sub_actions"></slot>
    </view>
</template>

<script lang="ts" setup>
import { useCopy } from '@/hooks/useCopy'
import { useAppStore } from '@/stores/app'
import TextItem from './text-item.vue'
import { isArray } from 'lodash-es'
import { computed } from 'vue'
import RecordImage from './record-image.vue'
import RecordFile from './record-file.vue'
// import
const props = withDefaults(
    defineProps<{
        recordId?: number | string
        type: 'left' | 'right'
        content: string
        showCopyBtn?: boolean
        showCollectBtn?: boolean
        showRewriteBtn?: boolean
        showPosterBtn?: boolean
        showVoiceBtn?: boolean
        loading?: boolean
        index?: number
        isCollect?: number
        avatar: string
        time?: string
        audio?: string
        chatType?: number
        images?: any[]
        files?: any[]
        videos?: any[]
        filesPlugin?: any[]
        bg?: string
        color?: string
        modelName?: string
        showPoster?: boolean
    }>(),
    {
        showCollectBtn: true,
        showCopyBtn: true,
        showRewriteBtn: false,
        showPosterBtn: false,
        content: '',
        loading: false,
        time: '',
        chatType: 1,
        context: () => [],
        quotes: () => [],
        images: () => [],
        files: () => [],
        videos: () => [],
        filesPlugin: () => [],
        showVoiceBtn: false,
        showPoster: false,
        modelName: ''
    }
)

const emit = defineEmits<{
    (event: 'close'): void
    (event: 'rewrite'): void
    (event: 'update', value: any): void
    (event: 'click-link', value: any): void
    (event: 'poster', value?: number | string): void
}>()

const appStore = useAppStore()
const { copy } = useCopy()

const isMarkdown = computed(() => {
    return props.type == 'left'
})
</script>

<style lang="scss" scoped>
@keyframes typingFade {
    0% {
        opacity: 0;
    }
    50% {
        opacity: 100%;
    }
    100% {
        opacity: 100%;
    }
}
.chat-record-item {
    padding: 0 20rpx;
    margin-bottom: 40rpx;
    &__left,
    &__right {
        display: flex;
        align-items: flex-start;
        min-height: 80rpx;
        &-content {
            display: inline-block;
            padding: 20rpx;
            max-width: 100%;
            border-radius: 10rpx;
            position: relative;
            min-width: 70rpx;
            min-height: 80rpx;
            &::before {
                content: '';
                display: block;
                width: 0;
                height: 0;
                position: absolute;
                top: 24rpx;
                border: 16rpx solid transparent;
            }
        }
        .text-typing {
            display: inline-block;
            vertical-align: -8rpx;
            height: 34rpx;
            width: 6rpx;
            background-color: $u-type-primary;
            animation: typingFade 0.4s infinite alternate;
        }
    }
    &__right {
        flex-direction: row-reverse;
    }

    &__left-content {
        margin-left: 25rpx;
        background-color: var(--bg-color, $-color-white);
        &::before {
            left: -30rpx;
            border-right-color: var(--bg-color, $-color-white);
        }
    }
    &__right-content {
        color: var(--color, #fff);
        background-color: var(--bg-color, #4073fa);
        margin-right: 20rpx;
        &::before {
            right: -30rpx;
            border-left-color: var(--bg-color, #4073fa);
        }
    }
}
</style>
