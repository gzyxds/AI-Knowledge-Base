// 充值套餐列表
export function getRechargeLists(params?: any) {
  return $request.get({ url: '/recharge/lists', params })
}

//充值下单
export function rechargePlace(params: any) {
  return $request.post({ url: '/recharge/place', params })
}

export function getRechargeRecord(params?: any) {
  return $request.get({ url: '/recharge/record', params })
}
