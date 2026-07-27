export default {
  extends: ['@commitlint/config-conventional'],
  rules: {
    'scope-enum': [
      2,
      'always',
      [
        // Default conventional types (valid sebagai scope juga)
        'build',
        'chore',
        'ci',
        'docs',
        'feat',
        'fix',
        'perf',
        'refactor',
        'revert',
        'style',
        'test',

        // Project-specific scopes
        'auth', // Keycloak, login, register
        'payment', // Pembayaran
        'report', // Laporan keuangan
        'settings', // Pengaturan
        'ui', // Frontend/UI changes
        'api', // API endpoints
        'db', // Database migrations/seeders
        'deps', // Dependencies update
        'docker', // Docker config
      ],
    ],
    'scope-case': [2, 'always', 'lower-case'],
    'header-max-length': [2, 'always', 100],
    'body-leading-blank': [1, 'always'],
    'body-max-line-length': [2, 'always', 150],
    'footer-leading-blank': [1, 'always'],
    'footer-max-line-length': [2, 'always', 150],
    'subject-empty': [2, 'never'],
    'subject-full-stop': [2, 'never', '.'],
    'subject-case': [
      2,
      'never',
      ['sentence-case', 'start-case', 'pascal-case', 'upper-case'],
    ],
  },
  helpUrl: 'https://www.conventionalcommits.org/en/v1.0.0/',
};
