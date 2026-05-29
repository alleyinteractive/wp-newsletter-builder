module.exports = {
  root: true,
  extends: ['@alleyinteractive/eslint-config/typescript-react'],
  parserOptions: {
    project: true,
    tsconfigRootDir: __dirname
  },
  rules: {
    'react-hooks/exhaustive-deps': ['error', {
      additionalHooks: '(useSelect|useDispatch)',
    }],
  },
};
