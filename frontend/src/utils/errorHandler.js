/**
 * Unified Frontend Error Normalization & Handling Utility
 */

export class ApiError extends Error {
  constructor(message, status = 500, errors = {}, code = 'UNKNOWN_ERROR') {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.errors = errors
    this.code = code
  }
}

/**
 * Normalize any error (Axios, Network, JS Error, String) into a standardized ApiError object
 * @param {any} error
 * @param {string} [locale='ar']
 * @returns {ApiError}
 */
export function normalizeError(error, locale = 'ar') {
  const isRtl = locale === 'ar'

  if (error instanceof ApiError) {
    return error
  }

  // Network or Connection Error
  if (!error.response && error.request) {
    const isTimeout = error.code === 'ECONNABORTED' || error.message?.includes('timeout')
    return new ApiError(
      isTimeout
        ? (isRtl ? 'انتهت مهلة الاتصال بالخادم، يرجى التحقق من اتصالك بالإنترنت والمحاولة مجدداً.' : 'Connection timed out. Please check your internet and try again.')
        : (isRtl ? 'تعذر الاتصال بالخادم الرئيسي، يرجى التأكد من اتصالك بالشبكة.' : 'Unable to connect to the server. Please check your network connection.'),
      0,
      {},
      isTimeout ? 'TIMEOUT_ERROR' : 'NETWORK_ERROR'
    )
  }

  // Axios HTTP Response Error
  if (error.response) {
    const { status, data } = error.response
    const serverMessage = data?.message || data?.error
    const fieldErrors = data?.errors || {}

    switch (status) {
      case 400:
        return new ApiError(
          serverMessage || (isRtl ? 'الطلب غير صالح أو تنقصه بعض البيانات.' : 'Bad request. Please verify the submitted data.'),
          400,
          fieldErrors,
          'BAD_REQUEST'
        )

      case 401:
        return new ApiError(
          serverMessage || (isRtl ? 'انتهت صلاحية الجلسة، يرجى تسجيل الدخول مجدداً.' : 'Session expired or unauthenticated. Please log in again.'),
          401,
          fieldErrors,
          'UNAUTHORIZED'
        )

      case 403:
        return new ApiError(
          serverMessage || (isRtl ? 'ليس لديك الصلاحيات الكافية لتنفيذ هذا الإجراء.' : 'You do not have permission to perform this action.'),
          403,
          fieldErrors,
          'FORBIDDEN'
        )

      case 404:
        return new ApiError(
          serverMessage || (isRtl ? 'العنصر المطلوب غير موجود أو تم حذفه.' : 'The requested resource was not found.'),
          404,
          fieldErrors,
          'NOT_FOUND'
        )

      case 409:
        return new ApiError(
          serverMessage || (isRtl ? 'يوجد تعارض في البيانات، قد يكون السجل مسجلاً مسبقاً.' : 'A conflict occurred. Record might already exist.'),
          409,
          fieldErrors,
          'CONFLICT'
        )

      case 422: {
        // Validation Error: Flatten field errors into a readable message if general message isn't descriptive
        let detailedMsg = serverMessage
        if (!detailedMsg || detailedMsg === 'The given data was invalid.') {
          const firstErrorKey = Object.keys(fieldErrors)[0]
          if (firstErrorKey && fieldErrors[firstErrorKey].length > 0) {
            detailedMsg = Array.isArray(fieldErrors[firstErrorKey]) 
              ? fieldErrors[firstErrorKey][0] 
              : fieldErrors[firstErrorKey]
          } else {
            detailedMsg = isRtl ? 'يرجى مراجعة البيانات المدخلة وتصحيح الأخطاء.' : 'Validation failed. Please correct the highlighted errors.'
          }
        }
        return new ApiError(
          detailedMsg,
          422,
          fieldErrors,
          'VALIDATION_ERROR'
        )
      }

      case 429:
        return new ApiError(
          isRtl ? 'تم إرسال عدد كبير جداً من الطلبات، يرجى الانتظار قليلاً.' : 'Too many requests. Please slow down and try again later.',
          429,
          fieldErrors,
          'RATE_LIMITED'
        )

      case 500:
      case 502:
      case 503:
      case 504:
        return new ApiError(
          isRtl ? 'حدث خطأ غير متوقع في الخادم، يرجى المحاولة في وقت لاحق.' : 'An internal server error occurred. Please try again later.',
          status,
          fieldErrors,
          'SERVER_ERROR'
        )

      default:
        return new ApiError(
          serverMessage || (isRtl ? 'حدث خطأ غير معروف.' : 'An unexpected error occurred.'),
          status,
          fieldErrors,
          'HTTP_ERROR'
        )
    }
  }

  // Standard JS Error or String
  return new ApiError(
    error?.message || (typeof error === 'string' ? error : (isRtl ? 'حدث خطأ غير متوقع.' : 'An unexpected error occurred.')),
    500,
    {},
    'GENERIC_ERROR'
  )
}
