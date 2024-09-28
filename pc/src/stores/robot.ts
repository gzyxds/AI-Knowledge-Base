import {
  cancelShare,
  clearRobotCate,
  delRobot,
  delRobotCate,
  getRobotCateLists,
  getRobotLists,
  postRobot,
  postRobotCate,
  putRobot,
  putRobotCate
} from '@/api/robot'
import { defineStore } from 'pinia'

interface RobotState {
  sessionId: number | string
  robotId: string
  robotLists: any[]
  sessionLists: any[]
}

export const useRobotStore = defineStore({
  id: 'robot',
  state: (): RobotState => {
    return {
      robotId: '',
      robotLists: [],
      sessionId: '',
      sessionLists: []
    }
  },
  getters: {
    getCurrentSession: (state) => {
      return (
        state.sessionLists.find(
          (item) => String(item.id) === state.sessionId
        ) || {}
      )
    },
    getCurrentApp: (state) => {
      return (
        state.robotLists.find((item) => String(item.id) === state.robotId) || {}
      )
    }
  },
  actions: {
    setRobotId(id: number | string = '') {
      this.robotId = String(id)
    },
    async getRobot(params?: any) {
      params = params || { page_type: 0 }
      const data = await getRobotLists(params)
      this.robotLists = data.lists

      if (this.robotLists.length > 0) {
        const [appFirst] = this.robotLists
        this.setRobotId(appFirst.id)
      }
      return data
    },
    async delRobot(id: number) {
      await feedback.confirm('确定删除？')
      await delRobot({ id })
    },
    async cancelShareRobot(id: number) {
      await feedback.confirm('确定取消分享吗？')
      await cancelShare({ id })
    },
    async addRobot() {
      const { id } = await postRobot()
      this.setRobotId(id)
      return id
    },
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
      const data = await getRobotCateLists({
        robot_id: this.robotId
      })
      this.sessionLists = data
      return this.sessionLists
    },
    async sessionAdd() {
      await postRobotCate({
        robot_id: this.robotId
      })
      await this.getSessionLists()
      this.setSessionSelect()
    },
    async sessionEdit(value: any) {
      await putRobotCate({
        ...value,
        robot_id: this.robotId
      })
      await this.getSessionLists()
    },
    async sessionClear() {
      await clearRobotCate({
        robot_id: this.robotId
      })
      await this.getSessionLists()
      this.setSessionSelect()
    },
    async sessionDelete(id: number) {
      await delRobotCate({
        id,
        robot_id: this.robotId
      })
      await this.getSessionLists()
      this.setSessionSelect()
    }
  }
})
