const Encore = require('@symfony/webpack-encore');
const api = Encore.default || Encore;

api.setOutputPath('public/build/');
api.setPublicPath('/build/');

api.addEntry('app', './assets/react/main.tsx');

api.enableReactPreset();
api.enableTypeScriptLoader();

// Ajout obligatoire pour Webpack Encore v7+
api.enableSingleRuntimeChunk();

api.enableSourceMaps(!api.isProduction());

module.exports = api.getWebpackConfig();
