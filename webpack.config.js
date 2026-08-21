const Encore = require('@symfony/webpack-encore');
const path = require('path');
const api = Encore.default || Encore;

api.setOutputPath('public/build/');
api.setPublicPath('/build/');

api.addEntry('app', './assets/react/main.tsx');
api.addEntry('email', './assets/styles/emails/email.scss');

// Alias @ -> assets
api.addAliases({
    '@': path.resolve(__dirname, 'assets'),
});

api.enableReactPreset();
api.enableTypeScriptLoader();
api.enableSassLoader();
api.configureCssLoader((options) => {
    options.modules = {
        auto: /\.module\.\w+$/i,
        localIdentName: '[local]__[hash:base64:5]',
        namedExport: false,
        exportLocalsConvention: 'as-is',
    };
});

// Ajout obligatoire pour Webpack Encore v7+
api.enableSingleRuntimeChunk();

api.enableSourceMaps(!api.isProduction());

module.exports = api.getWebpackConfig();
