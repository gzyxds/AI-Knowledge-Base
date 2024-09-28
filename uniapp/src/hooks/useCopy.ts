export function useCopy() {
    const copy = async (text: string) => {
        try {
            await uni.setClipboardData({
                data: String(text)
            })
        } catch (error) {
            uni.$u.toast(error)
        }
    }
    return {
        copy
    }
}
