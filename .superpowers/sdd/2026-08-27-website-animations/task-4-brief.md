---
noteId: "ba750580a20a11f199338946b8fe1c28"
tags: []

---

# Task 4 Brief: UI Micro-Interactions on Primitives

## Goal
Add rich, subtle academic micro-interactions and transitions to fundamental UI components (`Button.vue`, `Card.vue`, `Modal.vue`, `Badge.vue`).

## Target Files
- `frontend/src/components/ui/Button.vue` (modify)
- `frontend/src/components/ui/Card.vue` (modify)
- `frontend/src/components/ui/Modal.vue` (modify)
- `frontend/src/components/ui/Badge.vue` (modify)

## Requirements
1. **`Button.vue`**:
   - Enhance active click feedback: `active:scale-[0.98] transition-all duration-200`.
   - Add subtle hover glow for `gold`, `primary`, and `emerald` variants (`hover:shadow-md hover:shadow-gold-500/20`, etc.).
   - Ensure disabled/loading states stay crisp with `active:scale-100`.

2. **`Card.vue`**:
   - Enhance card hover physics: `hover:shadow-academic-lg hover:-translate-y-1 hover:border-slate-300/80 transition-all duration-300 ease-out`.

3. **`Modal.vue`**:
   - Refine modal transitions:
     - Backdrop fade transition (`transition-opacity duration-300`).
     - Modal content scale & translateY entry/exit (`transition-all duration-300 cubic-bezier(0.16, 1, 0.3, 1)`).
     - Enter from: `opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95`.
     - Enter to: `opacity-100 translate-y-0 sm:scale-100`.

4. **`Badge.vue`**:
   - Add optional `pulse` and `dot` props.
   - When `pulse` or `dot` is active, render a subtle animated pinging / pulsing status indicator dot with appropriate variant colors.

5. **Verification**:
   - Run `npm run build` in `frontend/` to ensure 0 errors.

6. **Report**:
   - Write report to `.superpowers/sdd/2026-08-27-website-animations/task-4-report.md`.
