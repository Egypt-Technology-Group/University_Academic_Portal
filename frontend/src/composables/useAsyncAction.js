import { ref } from 'vue'
import { useToast } from './useToast'
import { useLocaleStore } from '../stores/locale'
import { normalizeError } from '../utils/errorHandler'

/**
 * Enterprise composable for executing async operations with unified loading, 
 * error normalization, field error binding, retry capabilities, and toast notifications.
 */
export const useAsyncAction = () => {
  const isLoading = ref(false)
  const error = ref(null)
  const fieldErrors = ref({})
  const toast = useToast()
  const localeStore = useLocaleStore()

  /**
   * Execute an asynchronous action
   * @param {Function} actionFn - Async function to execute
   * @param {Object} options - Configuration options
   * @param {string} [options.successMessage] - Flash message on success
   * @param {string} [options.successTitle] - Flash title on success
   * @param {string} [options.errorMessage] - Fallback flash message on error
   * @param {string} [options.errorTitle] - Flash title on error
   * @param {boolean} [options.showSuccessToast=true] - Whether to trigger success toast
   * @param {boolean} [options.showErrorToast=true] - Whether to trigger error toast
   * @param {Function} [options.onSuccess] - Callback on success
   * @param {Function} [options.onError] - Callback on error
   * @returns {Promise<any>}
   */
  const execute = async (actionFn, options = {}) => {
    const {
      successMessage = '',
      successTitle = localeStore.isRtl ? 'تم بنجاح' : 'Success',
      errorMessage = '',
      errorTitle = localeStore.isRtl ? 'خطأ في العملية' : 'Action Failed',
      showSuccessToast = Boolean(successMessage),
      showErrorToast = true,
      onSuccess = null,
      onError = null,
    } = options

    isLoading.value = true
    error.value = null
    fieldErrors.value = {}

    try {
      const result = await actionFn()

      if (showSuccessToast && successMessage) {
        toast.success(successMessage, successTitle)
      }

      if (typeof onSuccess === 'function') {
        onSuccess(result)
      }

      return result
    } catch (err) {
      const normalized = normalizeError(err, localeStore.locale)
      error.value = normalized.message
      fieldErrors.value = normalized.errors || {}

      if (showErrorToast) {
        toast.error(errorMessage || normalized.message, errorTitle)
      }

      if (typeof onError === 'function') {
        onError(normalized)
      }

      throw normalized
    } finally {
      isLoading.value = false
    }
  }

  const clearErrors = () => {
    error.value = null
    fieldErrors.value = {}
  }

  return {
    isLoading,
    error,
    fieldErrors,
    execute,
    clearErrors,
  }
}
