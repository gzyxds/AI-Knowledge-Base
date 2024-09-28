export function useImageSplit() {
    const images = ref<string[]>([])

    const splitImage = (image: string) => {
        return new Promise((resolve, reject) => {
            const canvas: HTMLCanvasElement = document.createElement('canvas')
            const ctx: CanvasRenderingContext2D | null = canvas.getContext('2d')
            const img: HTMLImageElement = new Image()
            img.setAttribute('crossOrigin', '')

            img.onload = () => {
                const { width, height } = img
                const halfWidth = width / 2
                const halfHeight = height / 2

                canvas.width = halfWidth
                canvas.height = halfHeight

                images.value = []

                for (let x = 0; x < 2; x++) {
                    for (let y = 0; y < 2; y++) {
                        ctx?.clearRect(0, 0, halfWidth, halfHeight)
                        ctx?.drawImage(img, -x * halfWidth, -y * halfHeight)
                        // @ts-ignore
                        images.value.push(canvas?.toDataURL())
                    }
                }

                resolve(images.value)
            }

            img.onerror = (err) => {
                reject(err)
            }

            img.src = image
        })
    }

    return { images, splitImage }
}
