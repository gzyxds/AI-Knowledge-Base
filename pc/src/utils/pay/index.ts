import { Alipay } from './alipay'
import { Pay } from './pay'
import { Wechat } from './wechat'
// 支付方式
enum PayWayEnum {
  WECHAT = 2,
  ALIPAY = 3
}

const wechat = new Wechat()
// 注入微信支付
Pay.inject(PayWayEnum[2], wechat)
const alipay = new Alipay()
Pay.inject(PayWayEnum[3], alipay)
const pay = new Pay()
export { pay, PayWayEnum }
