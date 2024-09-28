import { ref } from "vue";

/**
 * @description 切割图片
 */
export const useImageSplit = () => {
    const images = ref<string[]>([]);

    // 获取图片
    const splitImage = (image: string) => {
        return new Promise((resolve, reject) => {
            // #ifdef H5
            const canvas: HTMLCanvasElement = document.createElement("canvas");
            const ctx: CanvasRenderingContext2D | null =
                canvas.getContext("2d");
            const img: HTMLImageElement = new Image();
            img.setAttribute("crossOrigin", "");

            img.onload = () => {
                const { width, height } = img;
                const halfWidth = width / 2;
                const halfHeight = height / 2;

                canvas.width = halfWidth;
                canvas.height = halfHeight;

                images.value = [];

                for (let x = 0; x < 2; x++) {
                    for (let y = 0; y < 2; y++) {
                        ctx?.clearRect(0, 0, halfWidth, halfHeight);
                        ctx?.drawImage(img, -x * halfWidth, -y * halfHeight);
                        // @ts-ignore
                        images.value.push(canvas?.toDataURL());
                    }
                }

                resolve(images.value);
            };

            img.onerror = (err) => {
                reject(err);
            };

            img.src = image;
            // #endif
            // #ifdef MP

            // 获取图片信息
            uni.getImageInfo({
                src: image,
                success: async (image: UniApp.GetImageInfoSuccessData) => {
                    // 创建一个离屏的canvas（也就是说不需要在标签页上使用
                    const canvas: UniApp.OffscreenCanvas =
                        uni.createOffscreenCanvas({
                            type: "2d",
                            width: image.width,
                            height: image.height,
                        });
                    const ctx: CanvasRenderingContext2D =
                        canvas.getContext("2d");
                    const img: HTMLImageElement = canvas.createImage();
                    // 等待图片加载
                    await new Promise((resolve) => {
                        img.src = image.path;
                        img.onload = resolve;
                    });

                    ctx.clearRect(0, 0, image.width, image.height);
                    ctx.drawImage(img, 0, 0, image.width, image.height);

                    const halfWidth: number = ctx.canvas.width / 2;
                    const halfHeight: number = ctx.canvas.height / 2;

                    images.value = [];

                    for (let x = 0; x < 2; x++) {
                        for (let y = 0; y < 2; y++) {
                            const path = getPart(
                                ctx,
                                y * halfWidth,
                                x * halfHeight,
                                halfWidth,
                                halfHeight
                            );
                            images.value.push(path);
                        }
                    }

                    resolve(images.value);
                },
                fail: (error) => {
                    reject(error);
                },
            });
            // #endif
        });
    };

    // #ifdef MP
    // 得到canvas的指定部分 MP
    const getPart = (
        ctx: CanvasRenderingContext2D,
        x: number,
        y: number,
        w: number,
        h: number
    ) => {
        const canvas: UniApp.OffscreenCanvas = uni.createOffscreenCanvas({
            type: "2d",
            width: w,
            height: h,
        });
        const context: CanvasRenderingContext2D = canvas.getContext("2d");
        const data: ImageData = ctx.getImageData(x, y, w, h);
        context.putImageData(data, 0, 0);
        return canvas.toDataURL("image/png", 1);
    };
    // #endif

    return { images, splitImage };
};
