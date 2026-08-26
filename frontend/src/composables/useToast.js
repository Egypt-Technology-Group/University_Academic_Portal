import { useToastStore } from '../stores/toast'

export const useToast = () => {
  const store = useToastStore()

  return {
    show: (opts) => store.show(opts),
    success: (msg, title, opts) => store.success(msg, title, opts),
    error: (msg, title, opts) => store.error(msg, title, opts),
    warning: (msg, title, opts) => store.warning(msg, title, opts),
    info: (msg, title, opts) => store.info(msg, title, opts),
    loading: (msg, title, opts) => store.loading(msg, title, opts),
    update: (id, opts) => store.update(id, opts),
    dismiss: (id) => store.dismiss(id),
    clear: () => store.clear(),
  }
}
