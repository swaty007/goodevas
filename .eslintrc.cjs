/* eslint-env node */
require('@rushstack/eslint-patch/modern-module-resolution');

module.exports = {
    env: {
        browser: true,
        node: true,
        es6: true,
    },
    root: true,
    extends: [
        '@vue/eslint-config-prettier',
        'plugin:vue/vue3-essential',
        'eslint:recommended',
        '@vue/eslint-config-typescript',
        'plugin:@typescript-eslint/eslint-recommended',
        'plugin:@typescript-eslint/recommended',
    ],
    parserOptions: {
        ecmaVersion: 'latest',
    },
    rules: {
        'vue/multi-word-component-names': 'warn',
        'no-undef': 'off',
        '@typescript-eslint/ban-ts-comment': 'off',
        'vue/html-indent': ['error', 4],
    },
};
