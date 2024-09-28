import VueQr from 'vue-qr/src/packages/vue-qr.vue'
import { createVNode } from 'vue'
import type { PayOptions } from './pay'
import { getOrderStatus } from '@/api/pay'
import { handleClientCallback } from '@/utils/client'
import wechatoa from '@/utils/wechat'
export class Wechat {
  init(name: string, pay: any) {
    pay[name] = this
  }

  run(options: PayOptions) {
    return new Promise((resolve, reject) => {
      handleClientCallback({
        PC: () => {
          this.sanCodePay(options, resolve, reject)
        },
        H5: () => {
          window.open(options.config, '_self')
        },
        WEIXIN_OA: () => {
            wechatoa.pay(options.config, resolve, reject)
        //   this.sanCodePay(options, resolve, reject)
        }
      })
    })
  }
  sanCodePay(options: PayOptions, resolve: any, reject: any) {
    const { start, end } = usePolling(
      async () => {
        const { pay_status } = await getOrderStatus({
          order_id: options.orderId,
          from: options.from
        })
        if (pay_status === 1) {
          resolve('success')
          ElMessageBox.close()
          end()
        }
      },
      {
        key: 'payment',
        totalTime: 300 * 1000,
        callback: () => {
          reject('支付超时')
          ElMessageBox.close()
          feedback.alertWarning('支付超时！')
        }
      }
    )
    start()
    this.showQrCode(options.config).catch(() => {
      end()
      reject('取消支付')
    })
  }
  async showQrCode(options: any) {
    const qrCode = createVNode(VueQr, {
      text: options,
      size: 160,
      dotScale: 1,
      margin: 0,
      style: {
        margin: '20px auto'
      }
    })
    const title = createVNode(
      'div',
      {
        style: {
          fontSize: '16px',
          color: '#333'
        }
      },
      '请使用微信扫一扫'
    )
    const tips1 = createVNode('div', null, '支付成功后自动关闭窗口')
    const tips2 = createVNode(
      'div',
      {
        style: {
          marginTop: '10px'
        }
      },
      '如遇到支付问题，请联系客服解决'
    )
    return ElMessageBox({
      title: '微信支付',
      showConfirmButton: false,
      closeOnClickModal: false,
      center: true,
      message: createVNode(
        'div',
        {
          style: {
            'text-align': 'center'
          }
        },
        [title, qrCode, tips1, tips2]
      )
    })
  }
}
