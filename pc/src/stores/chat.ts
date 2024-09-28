import { defineStore } from 'pinia'
import { useLocalStorage } from '@vueuse/core'
import { AUTHORIZATION_KEY, UUID_KEY } from '@/enums/cacheEnums'
import { chatChannelsAuth } from '~/api/knowledge'
interface ChatState {
  uuid: string
  token: string
  channelId: string
}

export const useChatStore = defineStore({
  id: 'chatStore',
  state: (): ChatState => {
    const uuid = useLocalStorage(UUID_KEY, '')
    if (!uuid.value) {
      uuid.value = uniqueId()
    }
    return {
      uuid: uuid.value || '',
      token: '',
      channelId: ''
    }
  },
  getters: {
    isAuth: (state) => !!state.token
  },
  actions: {
    init(channelId: string) {
      this.channelId = channelId
      const tokens = useLocalStorage<Record<string, string>>(
        AUTHORIZATION_KEY,
        {}
      )
      this.token = tokens.value[this.channelId] || ''
    },
    clearAuth() {
      const tokens = useLocalStorage<Record<string, string>>(
        AUTHORIZATION_KEY,
        {}
      )
      delete tokens.value[this.channelId]
      this.token = ''
    },
    async auth(password: string = '') {
      const oneDay = 24 * 60 * 60 * 1000
      const res = await chatChannelsAuth({
        password,
        channel: this.channelId,
        identity: this.uuid
      })
      this.token = res.token
      const tokens = useLocalStorage<Record<string, string>>(
        AUTHORIZATION_KEY,
        {}
      )
      tokens.value = Object.assign(
        {
          [this.channelId]: res.token
        },
        tokens.value
      )
    }
  }
})
