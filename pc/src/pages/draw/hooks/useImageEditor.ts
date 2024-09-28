import { ref, reactive, shallowReactive, onUnmounted } from 'vue';
import { DrawingTool } from './DrawingTool';

interface Callbacks {
    onData?(result: string): void;
}

interface CanvasAttributes {
    width: number | null;
    height: number | null;
    name: string;
    children: any[];
}

interface DrawToolMap {
    RECT: 'rect';
    LASSO: 'lasso';
}

export function useImageEditor(callbacks: Callbacks) {

    // 画布默认数据
    const defaultCanvasAttr = reactive<CanvasAttributes>({
        width: null,
        height: null,
        name: 'Stage',
        children: []
    });

    // 绘制工具集合
    const drawToolMap: DrawToolMap = {
        RECT: 'rect',
        LASSO: 'lasso'
    };

    // 画布参数
    const state = shallowReactive<{
        canvas: HTMLCanvasElement | null;
        ctx: CanvasRenderingContext2D | null;
        lineType: string;
        stepIndex: number;
        stepList: ImageData[];
        stepAttr: CanvasAttributes;
    }>({
        canvas: null,
        ctx: null,
        lineType: drawToolMap.LASSO,
        stepIndex: -1,
        stepList: [] ,
        stepAttr: defaultCanvasAttr
    });

    // 图片实力
    const image = ref(new Image());
    // 初始绘制工具
    const drawingTool = new DrawingTool();
    // 绘制选区路径的集合
    const selectionPaths = ref<any[]>([]);
    // 绘制坐标
    let drawPoints: number[] = [];
    // 鼠标是否按下
    let isMouseDown = false;

    /**
     * 初始化canvas和图片
     */
    const initCanvas = (canvasId: string, imageUrl: string) => {
        const canvas = document.getElementById(canvasId) as HTMLCanvasElement;
        if (!canvas) return;

        state.canvas = canvas;
        state.ctx = canvas.getContext('2d');
        canvas.style.cursor = 'crosshair';
        canvas.style.pointerEvents = 'none';

        if (state.ctx) {
            // 显示加载动画
            state.ctx.fillStyle = 'white';
            state.ctx.fillRect(0, 0, canvas.width, canvas.height);
            state.ctx.font = '20px Arial';
            state.ctx.fillStyle = 'black';
            state.ctx.fillText('Loading...', canvas.width / 2 - 40, canvas.height / 2);

            state.ctx.imageSmoothingEnabled = true // 为了提高性能，启用图像平滑处理
            state.ctx.imageSmoothingQuality = 'high' // 设置图像平滑处理质量

            // 图片加载完成
            image.value.onload = () => {
                canvas.style.pointerEvents = 'auto';
                const aspectRatio = image.value.width / image.value.height;
                let newWidth, newHeight;

                if (aspectRatio > canvas.width / canvas.height) {
                    newWidth = canvas.width;
                    newHeight = canvas.width / aspectRatio;
                } else {
                    newWidth = canvas.height * aspectRatio;
                    newHeight = canvas.height;
                }

                const x = (canvas.width - newWidth) / 2;
                const y = (canvas.height - newHeight) / 2;

                state.ctx?.drawImage(image.value, x, y, newWidth, newHeight);
                // 初始化绘画工具
                drawingTool.init(state);
                // 保存第一贞
                drawingTool.saveCurrentState();
            };

            // 图片加载失败
            image.value.onerror = (error) => {
                state.ctx?.clearRect(0, 0, canvas.width, canvas.height);
                state.ctx?.fillText('Image load failed', canvas.width / 2 - 40, canvas.height / 2);
            };

            image.value.setAttribute('crossOrigin', '')
            image.value.setAttribute('src', imageUrl)
        }
    };

    /**
     * 切换绘画工具
     */
    const changeTool = (tool: keyof DrawToolMap) => {
        state.lineType = drawToolMap[tool];
    };

    /**
     * 调用绘画工具
     */
    const handleDrawingTool = (...touch: any[]) => {
        const { x, y, w, h, color, move, closed } = touch[0];

        switch (state.lineType) {
            case drawToolMap.RECT:
                drawingTool.drawRect({ x, y, w: w - x, h: h - y, color });
                break;
            case drawToolMap.LASSO:
                if (move) drawPoints.push(w, h);
                drawingTool.drawLasso(drawPoints, color, 2, closed);
                break;
        }
    };

    /**
     * 手指触摸画布
     */
    const onMouseDown = (event: MouseEvent) => {
        isMouseDown = true;
        state.ctx?.beginPath();

        handleDrawingTool({
            x: event.offsetX,
            y: event.offsetY,
            color: '#11bdf7'
        });

        drawPoints = [event.offsetX, event.offsetY];
    };

    /**
     * 手指触摸移动画布
     */
    const onMouseMove = (event: MouseEvent) => {
        if (!isMouseDown) return;

        handleDrawingTool({
            x: drawPoints[0],
            y: drawPoints[1],
            w: event.offsetX,
            h: event.offsetY,
            color: '#11bdf7',
            move: true
        });
    };

    /**
     * 手指离开画布触摸
     */
    const onMouseUp = (event: MouseEvent) => {
        if (!drawPoints.length || (drawPoints[0] === event.offsetX && drawPoints[1] === event.offsetY)) {
            drawPoints = [];
            isMouseDown = false;
            // callbacks.onData?.('');
            return;
        }

        handleDrawingTool({
            x: drawPoints[0],
            y: drawPoints[1],
            w: event.offsetX,
            h: event.offsetY,
            closed: true
        });

        finalizeDrawing({ x: drawPoints[0], y: drawPoints[1], w: event.offsetX, h: event.offsetY });
        state.ctx?.closePath();
        drawingTool.saveCurrentState();

        isMouseDown = false;
    };

    /**
     * 绘画完成
     */
    const finalizeDrawing = (touch: any) => {
        const { x, y, w, h } = touch;

        switch (state.lineType) {
            case drawToolMap.RECT:
                captureRectArea({ x, y, w: w - x, h: h - y })
                break;
            case drawToolMap.LASSO:
                captureLassoArea(drawPoints)
                break;
        }

        drawPoints = [];
    };

    /**
     * 保存当前选区到选区路径集合中
     */
    const saveSelectionPath = (path: any) => {
        selectionPaths.value.push(path);
    };

    /**
     * 处理矩形绘制结束后事情
     * 现在他们将选区路径数据保存到selectionPaths数组中
     */
    const captureRectArea = ({ x, y, w, h }: any) => {
        // 每当绘制矩形选区后，保存这个选区
        saveSelectionPath({ type: drawToolMap.RECT, x, y, w, h })
    }

    /**
     * 处理套索选择绘制结束后事情
     * 现在他们将选区路径数据保存到selectionPaths数组中
     */
    const captureLassoArea = (points: number[]) => {
        // 每当绘制套索选区后，保存这个选区
        saveSelectionPath({ type: drawToolMap.LASSO, points: [...points] })
    }

    /**
     * 调整captureRectArea 和 captureLassoArea 函数
     * 将他们的功能合并到 captureCombinedSelections 中
     */
    /**
     * 调整 captureRectArea 和 captureLassoArea 函数
     * 将他们的功能合并到 captureCombinedSelections 中
     */
    const captureCombinedSelections = () => {
        if (!image.value) return;

        // 新建一个临时的 canvas，宽高设为原图的宽高
        const tempCanvas: HTMLCanvasElement = document.createElement('canvas');
        const tempCtx: CanvasRenderingContext2D | null = tempCanvas.getContext('2d');
        tempCanvas.width = image.value.width;  // 使用原图的宽度
        tempCanvas.height = image.value.height;  // 使用原图的高度

        // 设置背景为黑色
        tempCtx!.fillStyle = 'black';
        tempCtx!.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

        // 设置填充颜色为白色
        tempCtx!.fillStyle = 'white';

        // 绘制所有裁剪路径，需将坐标比例缩放到原图尺寸
        selectionPaths.value.forEach((path) => {
            if (path.type === drawToolMap.LASSO) {
                const points = path.points.map((coord: number, index: number) =>
                  index % 2 === 0
                    ? (coord / state.canvas!.width) * image.value.width
                    : (coord / state.canvas!.height) * image.value.height
                );

                tempCtx!.beginPath();
                tempCtx!.moveTo(points[0], points[1]);

                for (let i = 2; i < points.length; i += 2) {
                    tempCtx!.lineTo(points[i], points[i + 1]);
                }

                tempCtx!.closePath();
                // 使用填充将路径绘制到黑色背景上
                tempCtx!.fill();
            } else if (path.type === drawToolMap.RECT) {
                const x = (path.x / state.canvas!.width) * image.value.width;
                const y = (path.y / state.canvas!.height) * image.value.height;
                const w = (path.w / state.canvas!.width) * image.value.width;
                const h = (path.h / state.canvas!.height) * image.value.height;

                tempCtx!.beginPath();
                tempCtx!.rect(x, y, w, h);
                tempCtx!.closePath();
                // 使用填充将路径绘制到黑色背景上
                tempCtx!.fill();
            }
        });

        // 转换成 base64 图像并返回
        callbacks?.onData?.(tempCanvas!.toDataURL());
    };

    /**
     * 返回上一步
     */
    const undo = () => {
        if (state.stepIndex <= 0) return;
        state.stepIndex--;
        drawingTool.reapplyState();
        state.stepList.pop();
        selectionPaths.value.pop();
    };

    const clearState = () => {
        drawPoints = [];
        selectionPaths.value = [];
    };

    // 切换页面回来后固定第一贞页面
    window.addEventListener('focus', () => {
        state.canvas?.focus();
        state.ctx?.putImageData(state.stepList[state.stepIndex], 0, 0);
    });

    onUnmounted(clearState);

    return {
        initCanvas,
        currentTool: computed(() => state.lineType),
        changeTool,
        onMouseDown,
        onMouseMove,
        onMouseUp,
        undo,
        clearState,
        captureCombinedSelections
    };
}
