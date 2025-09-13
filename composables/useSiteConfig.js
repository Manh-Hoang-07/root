// composables/useSiteConfig.js
import { useSystemConfigV2 } from './useSystemConfigV2'

export const useSiteConfig = () => {
  const {
    config,
    loading,
    error,
    getGeneralConfig,
    siteName,
    siteLogo,
    siteEmail,
    sitePhone,
    siteAddress,
    siteDescription,
    timezone,
    language,
    maintenanceMode,
    itemsPerPage
  } = useSystemConfigV2()

  // Load config khi khởi tạo
  const initConfig = async () => {
    try {
      await getGeneralConfig()
    } catch (err) {
      console.error('Failed to load site config:', err)
    }
  }

  return {
    config,
    loading,
    error,
    initConfig,
    siteName,
    siteLogo,
    siteEmail,
    sitePhone,
    siteAddress,
    siteDescription,
    timezone,
    language,
    maintenanceMode,
    itemsPerPage
  }
}
