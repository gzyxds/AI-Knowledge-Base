import {
  ElLoading,
  ElMessage,
  ElMessageBox,
  type ElMessageBoxOptions,
  ElNotification
} from 'element-plus'
import type { LoadingInstance } from 'element-plus/es/components/loading/src/loading'

export class Feedback {
  private loadingInstance: LoadingInstance | null = null
  static instance: Feedback | null = null
  static getInstance() {
    return this.instance ?? (this.instance = new Feedback())
  }
  // 消息提示
  msg(msg: string) {
    return ElMessage.info(msg)
  }
  // 错误消息
  msgError(msg: string) {
    return ElMessage.error(msg)
  }
  // 成功消息
  msgSuccess(msg: string) {
    return ElMessage.success(msg)
  }
  // 警告消息
  msgWarning(msg: string) {
    return ElMessage.warning(msg)
  }
  // 弹出提示
  alert(msg: string) {
    return ElMessageBox.alert(msg, '系统提示')
  }
  // 错误提示
  alertError(msg: string) {
    return ElMessageBox.alert(msg, '系统提示', { type: 'error' })
  }
  // 成功提示
  alertSuccess(msg: string) {
    return ElMessageBox.alert(msg, '系统提示', { type: 'success' })
  }
  // 警告提示
  alertWarning(msg: string) {
    return ElMessageBox.alert(msg, '系统提示', { type: 'warning' })
  }
  // 通知提示
  notify(msg: string) {
    return ElNotification.info(msg)
  }
  // 错误通知
  notifyError(msg: string) {
    return ElNotification.error(msg)
  }
  // 成功通知
  notifySuccess(msg: string) {
    return ElNotification.success(msg)
  }
  // 警告通知
  notifyWarning(msg: string) {
    return ElNotification.warning(msg)
  }
  // 确认窗体
  confirm(msg: string, title = '温馨提示', options?: ElMessageBoxOptions) {
    return ElMessageBox.confirm(msg, title, {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning',
      ...options
    })
  }
  // 提交内容
  prompt(content: string, title: string, options?: ElMessageBoxOptions) {
    return ElMessageBox.prompt(content, title, {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      ...options
    })
  }
  // 打开全局loading
  loading(msg: string) {
    this.loadingInstance = ElLoading.service({
      lock: true,
      text: msg
    })
  }
  // 关闭全局loading
  closeLoading() {
    this.loadingInstance?.close()
  }
}

const feedback = Feedback.getInstance()

export default feedback
