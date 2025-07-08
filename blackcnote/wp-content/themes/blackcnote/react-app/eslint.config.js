import globals from 'globals';
import reactHooks from 'eslint-plugin-react-hooks';
import reactRefresh from 'eslint-plugin-react-refresh';
import tseslint from '@typescript-eslint/eslint-plugin';
import tsparser from '@typescript-eslint/parser';

export default [
  { ignores: [
    'dist',
    'dist/**',
    'node_modules',
    'node_modules/**',
    'backups',
    'backups/**',
    'blackcnote/dist',
    'blackcnote/dist/**',
    'reference',
    'reference/**',
    'hyiplab/assets',
    'hyiplab/assets/**',
    'blackcnote/assets',
    'blackcnote/assets/**',
    'scripts',
    'scripts/**',
    'dev-setup.js',
    'eslint.config.js',
    'postcss.config.js',
    'tailwind.config.js',
    'vite.config.ts',
    'vitest.config.ts',
    'bs-config.cjs',
    'hyiplab/assets/admin/js/vendor/*',
    'hyiplab/assets/global/js/*',
    'hyiplab/assets/public/js/*',
  ] },
  {
    files: ['**/*.{js,jsx}'],
    languageOptions: {
      ecmaVersion: 2020,
      globals: {
        ...globals.browser,
        jQuery: 'readonly',
        $: 'readonly',
        ApexCharts: 'readonly',
        Apex: 'readonly',
        SVG: 'readonly',
        require: 'readonly',
        module: 'readonly',
        define: 'readonly',
        global: 'readonly',
        process: 'readonly',
      },
    },
    plugins: {
      'react-hooks': reactHooks,
      'react-refresh': reactRefresh,
    },
    rules: {
      'no-unused-vars': 'warn',
      'no-undef': 'error',
      ...reactHooks.configs.recommended.rules,
      'react-refresh/only-export-components': [
        'warn',
        { allowConstantExport: true },
      ],
    },
  },
  {
    files: ['**/*.{ts,tsx}'],
    languageOptions: {
      ecmaVersion: 2020,
      globals: globals.browser,
      parser: tsparser,
      parserOptions: {
        ecmaVersion: 2020,
        sourceType: 'module',
        ecmaFeatures: {
          jsx: true,
        },
      },
    },
    plugins: {
      '@typescript-eslint': tseslint,
      'react-hooks': reactHooks,
      'react-refresh': reactRefresh,
    },
    rules: {
      '@typescript-eslint/no-unused-vars': 'warn',
      '@typescript-eslint/no-explicit-any': 'warn',
      ...reactHooks.configs.recommended.rules,
      'react-refresh/only-export-components': [
        'warn',
        { allowConstantExport: true },
      ],
    },
  },
];
