export default defineNuxtPlugin((nuxtApp) => {

    const isMobileDevice = () => {
        const userAgent = navigator?.userAgent || navigator?.vendor || (window as any)?.opera;

        // 简单的移动端检测
        return /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(userAgent);
    };

    const redirectToMobileSite = () => {
        if (window.location.pathname == '/') {
            window.location.href = '/mobile/' + location.search
        }
    };

    nuxtApp.hook('app:mounted', () => {
        if (isMobileDevice()) {
            redirectToMobileSite();
        }
    });
});