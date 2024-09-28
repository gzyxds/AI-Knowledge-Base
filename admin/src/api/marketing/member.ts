import request from '@/utils/request'
import type { MemberRequest } from './member_d'

//会员套餐配置
export function getMemberConfig() {
    return request.get({ url: '/member.MemberPackage/getConfig' })
}

//设置会员套餐配置
export function setMemberConfig(params: { is_open: 0 | 1 }) {
    return request.post({ url: '/member.MemberPackage/setConfig', params })
}

//会员套餐列表
export function memberLists(params: any) {
    return request.get({ url: '/member.memberPackage/lists', params })
}
//添加会员套餐
export function memberAdd(params: MemberRequest) {
    return request.post({ url: '/member.memberPackage/add', params })
}
//删会员套餐
export function memberDel(params: { id: number }) {
    return request.post({ url: '/member.memberPackage/del', params })
}
//会员套餐详情
export function memberDetail(params: any) {
    return request.get({ url: '/member.memberPackage/detail', params })
}
//编辑会员套餐
export function memberEdit(params: MemberRequest) {
    return request.post({ url: '/member.memberPackage/edit', params })
}
export function updateStatus(params: { id: number }) {
    return request.post({ url: '/member.memberPackage/status', params })
}
export function updateSort(params: { id: number, sort: number }) {
    return request.post({ url: '/member.memberPackage/sort', params })
}
export function updateRecommend(params: { id: number }) {
    return request.post({ url: '/member.memberPackage/recommend', params })
}
//套餐模型
export function memberModelLists() {
    return request.get({ url: '/member.memberPackage/getModels' })
}


//开启关闭会员套餐
export function getConfig(params?: any) {
    return request.get({ url: '/member.memberPackage/getConfig', params })
}
export function setConfig(data: any) {
    return request.post({ url: '/member.memberPackage/setConfig', data })
}

//会员权益列表
export function getMemberBenefits(params: any) {
    return request.get({ url: '/member.member_benefits/lists', params })
}

export function memberBenefitsDetail(params: any) {
    return request.get({ url: '/member.member_benefits/detail', params })
}

// 添加会员权益
export function memberBenefitsAdd(params: any) {
    return request.post({ url: '/member.member_benefits/add', params })
}

// 编辑会员权益
export function memberBenefitsEdit(params: any) {
    return request.post({ url: '/member.member_benefits/edit', params })
}

// 删除会员权益
export function memberBenefitsDelete(params: any) {
    return request.post({ url: '/member.member_benefits/del', params })
}

// 调整会员权益状态
export function memberBenefitsStatus(params: any) {
    return request.post({ url: '/member.member_benefits/status', params })
}

export function getBenefitsListsAll() {
    return request.get({ url: '/member.memberPackage/benefitsLists' })
}
