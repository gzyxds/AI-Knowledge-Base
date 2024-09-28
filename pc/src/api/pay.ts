//获取支付方式
export function getPayWayList(params?: any) {
  return $request.get({ url: '/pay/payWay', params })
}

//支付
export function prepay(params?: any) {
  return $request.post({ url: '/pay/prepay', params })
}

//订单状态
export function getOrderStatus(params?: any) {
  return $request.get({ url: '/pay/payStatus', params })
}
