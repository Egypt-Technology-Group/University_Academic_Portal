import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import i18n from './i18n'
import './style.css'
import { useSettingsStore } from './stores/settings'
import { useModulesStore } from './stores/modules'

// Core Micro-Module Registry & Module Definitions
import { moduleRegistry } from './core/modules/moduleRegistry'
import AcademicStructureModule from './modules/academic-structure'
import AdmissionsModule from './modules/admissions'
import AcademicServicesModule from './modules/academic-services'
import CmsModule from './modules/cms'
import EventsModule from './modules/events'
import DocumentsModule from './modules/documents'
import ResultsModule from './modules/results'

// Register all modular plugins into client runtime registry
moduleRegistry.registerAll([
  AcademicStructureModule,
  AdmissionsModule,
  AcademicServicesModule,
  CmsModule,
  EventsModule,
  DocumentsModule,
  ResultsModule,
])

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(i18n)

// Apply cached theme colors & hydrate module manifest before DOM mount
const settingsStore = useSettingsStore(pinia)
settingsStore.applyThemeToCssVariables()

const modulesStore = useModulesStore(pinia)
modulesStore.hydrateFromCache()

app.mount('#app')
