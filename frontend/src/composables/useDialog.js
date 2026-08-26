import { reactive } from 'vue'

const state = reactive({
  isOpen: false,
  type: 'confirm', // 'alert' | 'confirm' | 'prompt'
  title: '',
  message: '',
  variant: 'danger', // 'danger' | 'warning' | 'info' | 'success'
  confirmText: '',
  cancelText: '',
  placeholder: '',
  defaultValue: '',
  inputValue: '',
  inputLabel: '',
  isLoading: false,
  resolve: null,
})

export const useDialog = () => {
  const confirm = ({
    title = '',
    message = '',
    variant = 'danger',
    confirmText = '',
    cancelText = '',
  } = {}) => {
    return new Promise((resolve) => {
      state.type = 'confirm'
      state.title = title
      state.message = message
      state.variant = variant
      state.confirmText = confirmText
      state.cancelText = cancelText
      state.inputValue = ''
      state.isLoading = false
      state.isOpen = true
      state.resolve = resolve
    })
  }

  const alert = ({
    title = '',
    message = '',
    variant = 'info',
    confirmText = '',
  } = {}) => {
    return new Promise((resolve) => {
      state.type = 'alert'
      state.title = title
      state.message = message
      state.variant = variant
      state.confirmText = confirmText
      state.cancelText = ''
      state.inputValue = ''
      state.isLoading = false
      state.isOpen = true
      state.resolve = resolve
    })
  }

  const prompt = ({
    title = '',
    message = '',
    inputLabel = '',
    defaultValue = '',
    placeholder = '',
    confirmText = '',
    cancelText = '',
    variant = 'info',
  } = {}) => {
    return new Promise((resolve) => {
      state.type = 'prompt'
      state.title = title
      state.message = message
      state.inputLabel = inputLabel
      state.defaultValue = defaultValue
      state.inputValue = defaultValue
      state.placeholder = placeholder
      state.confirmText = confirmText
      state.cancelText = cancelText
      state.variant = variant
      state.isLoading = false
      state.isOpen = true
      state.resolve = resolve
    })
  }

  const handleConfirm = () => {
    state.isOpen = false
    if (state.resolve) {
      if (state.type === 'prompt') {
        state.resolve(state.inputValue)
      } else if (state.type === 'confirm') {
        state.resolve(true)
      } else {
        state.resolve(true)
      }
      state.resolve = null
    }
  }

  const handleCancel = () => {
    state.isOpen = false
    if (state.resolve) {
      if (state.type === 'prompt') {
        state.resolve(null)
      } else {
        state.resolve(false)
      }
      state.resolve = null
    }
  }

  return {
    state,
    confirm,
    alert,
    prompt,
    handleConfirm,
    handleCancel,
  }
}
