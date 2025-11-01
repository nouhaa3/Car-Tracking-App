import { ref, reactive, watch, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import dayjs from 'dayjs';
import 'dayjs/locale/fr';
import 'dayjs/locale/en';
import 'dayjs/locale/ar';

// Default settings
const defaultSettings = {
  // General
  language: 'fr',
  dateFormat: 'DD/MM/YYYY',
  time24h: true,
  currency: 'TND',
  useMiles: false,
  fuelUnit: 'L/100km',
  useGallons: false,
  defaultVehicle: '',
  
  // Display
  fontSize: 'medium',
  viewDensity: 'comfortable',
  
  // Notifications
  notificationsEnabled: true,
  expirationNoticeDays: '15',
  notificationSound: true,
  quietHoursStart: '22:00',
  quietHoursEnd: '08:00',
  
  // Data
  autoBackupFrequency: 'weekly',
  
  // Security
  autoLockTimeout: '15',
  requirePasswordOnLaunch: false,
  dataSharing: false,
};

// Load settings from localStorage
const loadSettings = () => {
  try {
    const saved = localStorage.getItem('appSettings');
    if (saved) {
      return { ...defaultSettings, ...JSON.parse(saved) };
    }
  } catch (error) {
    console.error('Error loading settings:', error);
  }
  return { ...defaultSettings };
};

// Global settings state
const settings = reactive(loadSettings());

// Save settings to localStorage
const saveToStorage = (newSettings) => {
  try {
    localStorage.setItem('appSettings', JSON.stringify(newSettings));
  } catch (error) {
    console.error('Error saving settings:', error);
  }
};

// Helper functions that don't require Vue context
const applyFontSize = (size) => {
  const root = document.documentElement;
  switch (size) {
    case 'small':
      root.style.fontSize = '14px';
      break;
    case 'large':
      root.style.fontSize = '18px';
      break;
    default: // medium
      root.style.fontSize = '16px';
  }
};

const applyViewDensity = (density) => {
  const root = document.documentElement;
  if (density === 'compact') {
    root.classList.add('density-compact');
    root.classList.remove('density-comfortable');
  } else {
    root.classList.add('density-comfortable');
    root.classList.remove('density-compact');
  }
};

const applyLanguageAndRTL = (language) => {
  dayjs.locale(language);
  
  if (language === 'ar') {
    document.documentElement.setAttribute('dir', 'rtl');
    document.documentElement.setAttribute('lang', 'ar');
  } else {
    document.documentElement.setAttribute('dir', 'ltr');
    document.documentElement.setAttribute('lang', language);
  }
};

// Initialize settings on module load (before any component mounts)
applyFontSize(settings.fontSize);
applyViewDensity(settings.viewDensity);
applyLanguageAndRTL(settings.language);

export function useSettings() {
  const { locale } = useI18n();

  // Sync i18n locale with settings
  if (locale.value !== settings.language) {
    locale.value = settings.language;
  }

  // Update settings
  const updateSettings = (newSettings) => {
    Object.assign(settings, newSettings);
    saveToStorage(settings);
    
    // Apply changes immediately
    if (newSettings.language) {
      locale.value = newSettings.language;
      applyLanguageAndRTL(newSettings.language);
    }
    
    if (newSettings.fontSize) {
      applyFontSize(newSettings.fontSize);
    }
    
    if (newSettings.viewDensity) {
      applyViewDensity(newSettings.viewDensity);
    }
  };

  // Format date according to settings
  const formatDate = (date, customFormat = null) => {
    if (!date) return '';
    const format = customFormat || settings.dateFormat;
    return dayjs(date).format(format);
  };

  // Format time according to settings
  const formatTime = (time, showSeconds = false) => {
    if (!time) return '';
    const format = settings.time24h 
      ? (showSeconds ? 'HH:mm:ss' : 'HH:mm')
      : (showSeconds ? 'hh:mm:ss A' : 'hh:mm A');
    return dayjs(time).format(format);
  };

  // Format datetime
  const formatDateTime = (datetime) => {
    if (!datetime) return '';
    return `${formatDate(datetime)} ${formatTime(datetime)}`;
  };

  // Format currency
  const formatCurrency = (amount) => {
    if (amount === null || amount === undefined) return '';
    
    const formatter = new Intl.NumberFormat(settings.language === 'ar' ? 'ar-TN' : settings.language, {
      style: 'currency',
      currency: settings.currency,
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
    
    return formatter.format(amount);
  };

  // Convert distance (km to miles if needed)
  const formatDistance = (km) => {
    if (km === null || km === undefined) return '';
    
    if (settings.useMiles) {
      const miles = km * 0.621371;
      return `${miles.toFixed(1)} mi`;
    }
    return `${km.toFixed(1)} km`;
  };

  // Convert volume (liters to gallons if needed)
  const formatVolume = (liters) => {
    if (liters === null || liters === undefined) return '';
    
    if (settings.useGallons) {
      const gallons = liters * 0.264172;
      return `${gallons.toFixed(2)} gal`;
    }
    return `${liters.toFixed(2)} L`;
  };

  // Format fuel consumption
  const formatFuelConsumption = (consumption) => {
    if (consumption === null || consumption === undefined) return '';
    
    switch (settings.fuelUnit) {
      case 'MPG':
        // Convert L/100km to MPG (US)
        const mpg = 235.214 / consumption;
        return `${mpg.toFixed(1)} MPG`;
      case 'km/L':
        const kmPerLiter = 100 / consumption;
        return `${kmPerLiter.toFixed(1)} km/L`;
      default: // L/100km
        return `${consumption.toFixed(1)} L/100km`;
    }
  };

  // Check if we're in quiet hours
  const isQuietHours = computed(() => {
    if (!settings.notificationsEnabled) return true;
    
    const now = dayjs();
    const start = dayjs(settings.quietHoursStart, 'HH:mm');
    const end = dayjs(settings.quietHoursEnd, 'HH:mm');
    
    // Handle overnight quiet hours (e.g., 22:00 to 08:00)
    if (start.isAfter(end)) {
      return now.isAfter(start) || now.isBefore(end);
    }
    
    return now.isAfter(start) && now.isBefore(end);
  });

  // Check if notifications are allowed
  const canNotify = computed(() => {
    return settings.notificationsEnabled && !isQuietHours.value;
  });

  // Play notification sound
  const playNotificationSound = () => {
    if (settings.notificationSound && canNotify.value) {
      // You can add actual sound file here
      // new Audio('/sounds/notification.mp3').play();
      console.log('🔔 Notification sound');
    }
  };

  // Get expiration notice days as number
  const expirationNoticeDays = computed(() => {
    return parseInt(settings.expirationNoticeDays) || 15;
  });

  return {
    settings,
    updateSettings,
    formatDate,
    formatTime,
    formatDateTime,
    formatCurrency,
    formatDistance,
    formatVolume,
    formatFuelConsumption,
    isQuietHours,
    canNotify,
    playNotificationSound,
    expirationNoticeDays,
  };
}
