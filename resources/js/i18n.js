import { createI18n } from 'vue-i18n';
import fr from './locales/fr';
import en from './locales/en';
import ar from './locales/ar';

// Get saved language from settings or default to French
const savedSettings = localStorage.getItem('appSettings');
const defaultLocale = savedSettings ? JSON.parse(savedSettings).language || 'fr' : 'fr';

const i18n = createI18n({
  legacy: false,
  locale: defaultLocale,
  fallbackLocale: 'fr',
  messages: {
    fr,
    en,
    ar,
  },
  globalInjection: true,
});

export default i18n;
