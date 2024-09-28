import { defineStore } from 'pinia'

interface State {
  show: boolean
}

export const useRechargeStore = defineStore({
  id: 'rechargeStore',
  state: (): State => {
    return {
      show: false
    }
  },
  getters: {},
  actions: {
    toggleShow(toggle?: boolean) {
      this.show = toggle ?? !this.show
    }
  }
})
