import { PayWayEnum } from '.'
export interface PayOptions {
  payWay: PayWayEnum
  orderId: string | number
  config: any
  from: string
}

export class Pay {
  [x: string]: any
  // 维护模块名单
  private static modules = new Map()
  static inject(name: string, module: any) {
    this.modules.set(name, module)
  }
  constructor() {
    //动态注入支付方式
    for (const [name, module] of Pay.modules.entries()) {
      module.init(name, this)
    }
  }
  //调用支付
  async run(options: PayOptions) {
    try {
      //@ts-ignore
      const module = this[PayWayEnum[options.payWay]]
      // console.log(module)
      if (!module) {
        return Promise.reject(`can not find pay way ${options.payWay}`)
      }
      return await module.run(options)
    } catch (error) {
      return Promise.reject(error)
    }
  }
}
