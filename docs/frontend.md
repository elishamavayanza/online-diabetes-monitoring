# Frontend (React + TypeScript)

Le dépôt embarque un **socle frontend minimal** intégré à Symfony via **Webpack Encore** et **UX React**. Aucun écran métier n'est encore implémenté : l'application React consomme l'API DiabCare qui est le cœur du dépôt.

## 1. Stack

| Brique | Version |
|---|---|
| React / ReactDOM | 19.x |
| TypeScript | 5.9 |
| Webpack Encore | 7.x |
| `@symfony/ux-react` + Stimulus | — |

## 2. Structure

```text
assets/
├── controllers.json                # UX React activé (eager)
├── controllers/
│   ├── csrf_protection_controller.js
│   └── hello_controller.js         # exemple Stimulus
├── react/
│   ├── main.tsx                    # point d'entrée (mount sur #root)
│   ├── components/App.tsx          # composant placeholder
│   └── controllers/Hello.jsx       # exemple
├── stimulus_bootstrap.js
└── react (entrée webpack)

public/build/                       # sortie compilée (app.js, runtime.js, manifest…)
```

## 3. Configuration

- **`webpack.config.js`** : entrée unique `app` → `assets/react/main.tsx` ; output `public/build/` ; preset React + loader TypeScript ; single runtime chunk ; source maps en dev.
- **`tsconfig.json`** : target ES2020, module ESNext, `jsx: react-jsx`, `strict: true`, `moduleResolution: Bundler`.
- **`templates/base.html.twig`** : unique template Twig (support du hot reload FrankenPHP).

## 4. Commandes

```bash
npm run dev      # build de développement (watch)
npm run build    # build de production
```

## 5. État actuel

- Une seule entrée et un seul composant placeholder (`App.tsx` affiche « DiabCare React + TypeScript fonctionne 🚀 »).
- Aucun écran métier, aucune intégration avec l'API.
- L'interface utilisateur finale est développée séparément et consommera les endpoints REST documentés dans [architecture/API_ENDPOINTS.md](./architecture/API_ENDPOINTS.md).
