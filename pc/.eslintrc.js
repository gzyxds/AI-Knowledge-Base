/* eslint-env node */
module.exports = {
  root: true,
  extends: [
    'plugin:nuxt/recommended',
    'plugin:vue/vue3-essential',
    'eslint:recommended',
    '@vue/eslint-config-typescript/recommended'
  ],
  parserOptions: {
    ecmaVersion: 'latest',
    parser: '@typescript-eslint/parser',
    sourceType: 'module'
  },
  plugins: ['@typescript-eslint'],
  rules: {
    'no-empty': 'off',
    'no-useless-escape': 'off',
    'vue/multi-word-component-names': 'off',
    '@typescript-eslint/no-explicit-any': 'off',
    '@typescript-eslint/ban-ts-comment': 'off',
    'no-undef': 'off',
    'vue/prefer-import-from-vue': 'off',
    'no-prototype-builtins': 'off',
    'prefer-spread': 'off',
    '@typescript-eslint/no-non-null-assertion': 'off',
    '@typescript-eslint/no-non-null-asserted-optional-chain': 'off',
    '@typescript-eslint/no-unused-vars': 'off',
    'vue/no-unused-vars': 'off'
  },
  globals: {
    module: 'readonly'
  }
}
