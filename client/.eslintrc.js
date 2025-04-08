module.exports = {
    root: true,
    env: {
      browser: true,
      es2021: true
    },
    extends: [
      'plugin:vue/vue3-recommended', // 👈 Vue 3 specific rules
      'eslint:recommended'
    ],
    parserOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module'
    },
    plugins: ['vue'],
    rules: {
      // You can customize rules here
    }
}
  