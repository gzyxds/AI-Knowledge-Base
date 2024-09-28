import VueTinymce from '@tinymce/tinymce-vue';

export default defineNuxtPlugin(nuxtApp => {
  nuxtApp.vueApp.use(VueTinymce as any);
});