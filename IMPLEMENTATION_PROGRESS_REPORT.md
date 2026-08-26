# Site Statistics & Numerical Counters Management Subsystem
**Project:** EgyiTech University Academic Portal  
**Status:** **100% Implemented & Verified Dynamic Integration**

---

## 1. Subsystem Architecture

A dedicated **Site Statistics & Numerical Counters** subsystem was designed and implemented across database seeders, API endpoints, Pinia store getters, the Admin Settings control panel, and public visitor pages.

### A. Dynamic Data Schema & Persistence
- **Setting Key:** `site_statistics` stored in database via `SiteSetting` model with JSON value structure:
  ```json
  {
    "title": { "ar": "جامعة إيجي تك في أرقام", "en": "EgyiTech at a Glance" },
    "subtitle": { "ar": "إنجازات تبرز التميز الأكاديمي والريادة الوطنية والبحثية", "en": "Milestones demonstrating academic prestige, research excellence, and national impact" },
    "items": [
      {
        "id": "students",
        "label": { "ar": "طالب وطالبة مقيدين", "en": "Enrolled Students" },
        "value": "15,400+",
        "prefix": "",
        "suffix": "+",
        "icon": "Users",
        "color": "gold",
        "is_active": true,
        "order": 1
      },
      {
        "id": "faculty",
        "label": { "ar": "عضو هيئة تدريس وباحث", "en": "Faculty & Researchers" },
        "value": "480+",
        "prefix": "",
        "suffix": "+",
        "icon": "GraduationCap",
        "color": "emerald",
        "is_active": true,
        "order": 2
      },
      {
        "id": "programs",
        "label": { "ar": "برنامج أكاديمي معتمد", "en": "Accredited Programs" },
        "value": "28",
        "prefix": "",
        "suffix": "",
        "icon": "BookOpen",
        "color": "gold",
        "is_active": true,
        "order": 3
      },
      {
        "id": "employment",
        "label": { "ar": "نسبة توظيف الخريجين", "en": "Graduate Employment Rate" },
        "value": "96.8%",
        "prefix": "",
        "suffix": "%",
        "icon": "Award",
        "color": "emerald",
        "is_active": true,
        "order": 4
      },
      {
        "id": "research",
        "label": { "ar": "بحث علمي منشور دولياً", "en": "Global Indexed Publications" },
        "value": "1,350+",
        "prefix": "",
        "suffix": "+",
        "icon": "FileText",
        "color": "gold",
        "is_active": true,
        "order": 5
      },
      {
        "id": "partners",
        "label": { "ar": "شريك صناعي وتكنولوجي", "en": "Industrial & Tech Partners" },
        "value": "65+",
        "prefix": "",
        "suffix": "+",
        "icon": "Building2",
        "color": "emerald",
        "is_active": true,
        "order": 6
      }
    ]
  }
  ```

### B. Admin Management Interface ([`AdminSettingsView.vue`](file:///D:/coding/projects/web%20developer/Laravel/EgyiTech/University_Academic_Portal/frontend/src/views/admin/AdminSettingsView.vue))
- Added **TAB 7: Site Statistics & Numerical Counters** with:
  - Custom section titles and subtitles in Arabic & English.
  - Interactive metric items list supporting **Create**, **Edit**, **Delete**, **Reorder (Up/Down)**, and **Enable/Disable** toggles.
  - Value input (`value`, e.g. `15,400+`, `96.8%`, `28`), Arabic label, English label, and color accent selection (`gold`, `emerald`, `sky`, `white`).

### C. Public Site Binding ([`HomeView.vue`](file:///D:/coding/projects/web%20developer/Laravel/EgyiTech/University_Academic_Portal/frontend/src/views/HomeView.vue))
- Removed all hardcoded metric counters.
- Bound directly to `settingsStore.activeStatisticsItems`, rendering dynamic metrics in real time according to their active state, sort order, and color accents.

---

## 2. Verification Evidence

- **Database Persistence:** Executed `php artisan db:seed --class=SiteSettingsSeeder` successfully.
- **Frontend Production Build:** `npm run build` completed in **2.00s** with **0 errors, exit code 0** (1,919 modules transformed).
- **PHP Syntax Validation:** Clean syntax across modified backend files with **0 errors**.
