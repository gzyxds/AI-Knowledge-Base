import { defineStore } from 'pinia'
import { useUserStore } from './user'
import {
  chatCategoryAdd,
  chatCategoryClear,
  chatCategoryDelete,
  chatCategoryEdit,
  chatSendText,
  cleanChatRecord,
  getChatCategoryLists,
  getChatRecord
} from '~/api/chat'
interface DialogueState {
  sessionId: number | string
  sessionLists: any[]
}
export const useDialogueStore = defineStore({
  id: 'dialogueSate',
  state: (): DialogueState => {
    return {
      sessionId: '',
      sessionLists: []
    }
  },
  getters: {
    getCurrentSession: (state) => {
      return (
        state.sessionLists.find(
          (item) => String(item.id) === String(state.sessionId)
        ) || {}
      )
    }
  },
  actions: {
    setSessionId(id: number | string = '') {
      this.sessionId = String(id)
    },
    setSessionSelect(item?: any) {
      if (!item) {
        [item] = this.sessionLists
      }
      this.setSessionId(item?.id || '')
    },
    async getSessionLists() {
      const data = await getChatCategoryLists({
        page_type: 0
      })
      this.sessionLists = data.lists || []
      this.setSessionSelect()
      return this.sessionLists
    },
    async sessionAdd() {
      await chatCategoryAdd({})
      await this.getSessionLists()
      this.setSessionSelect()
    },
    async sessionEdit(value: any) {
      await chatCategoryEdit({
        ...value
      })
      await this.getSessionLists()
      this.setSessionSelect(value)
    },
    async sessionClear() {
      await chatCategoryClear()
      await this.getSessionLists()
    },
    async sessionDelete(id: number) {
      await chatCategoryDelete({
        id
      })
      await this.getSessionLists()
    }
  }
})
