var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('main', './assets/js/main.js')
    // frontend
    .addEntry('frontend', './assets/js/frontend/app.js')
    .addEntry('home', './assets/js/frontend/home.js')
    .addEntry('login', './assets/js/login.js')
    .addEntry('assets', './assets/js/frontend/assets/index.js')
    .addEntry('assets/view', './assets/js/frontend/assets/view.js')
    .addEntry('sales', './assets/js/frontend/sales/index.js')
    .addEntry('sales/view', './assets/js/frontend/sales/view.js')
    .addEntry('renting', './assets/js/frontend/renting/index.js')
    .addEntry('renting/add', './assets/js/frontend/renting/add.js')
    .addEntry('renting/view', './assets/js/frontend/renting/view.js')
    .addEntry('projects', './assets/js/frontend/projects/index.js')
    .addEntry('projects/view', './assets/js/frontend/projects/view.js')
    .addEntry('selling', './assets/js/frontend/selling/index.js')
    .addEntry('letting', './assets/js/frontend/letting/index.js')
    .addEntry('subscribe', './assets/js/frontend/subscribe.js')
    // dashboard
    .addEntry('dashboard', './assets/js/dashboard/app.js')
    .addEntry('dashboard/home', './assets/js/dashboard/home.js')
    .addEntry('dashboard/calendar', './assets/js/dashboard/calendar/index.js')
    .addEntry('dashboard/booking', './assets/js/dashboard/booking/index.js')
    .addEntry('dashboard/schedule', './assets/js/dashboard/schedule/index.js')
    .addEntry('dashboard/profile/account', './assets/js/dashboard/profile/account.js')
    .addEntry('dashboard/security/users/form', './assets/js/dashboard/security/users/form.js')
    .addEntry('dashboard/sales/properties/index', './assets/js/dashboard/sales/properties/index.js')
    .addEntry('dashboard/sales/properties/form', './assets/js/dashboard/sales/properties/form.js')
    .addEntry('dashboard/offers/index', './assets/js/dashboard/offers/index.js')
    .addEntry('dashboard/offers/form', './assets/js/dashboard/offers/form.js')
    .addEntry('dashboard/renting/accommodations/index', './assets/js/dashboard/renting/accommodations/index.js')
    .addEntry('dashboard/renting/accommodations/form', './assets/js/dashboard/renting/accommodations/form.js')
    .addEntry('dashboard/renting/reserves/index', './assets/js/dashboard/renting/reserves/index.js')
    .addEntry('dashboard/tenant/offers', './assets/js/dashboard/tenant/offers.js')
    .addEntry('dashboard/projects/projects/index', './assets/js/dashboard/projects/projects/index.js')
    .addEntry('dashboard/projects/projects/form', './assets/js/dashboard/projects/projects/form.js')
    .addEntry('dashboard/projects/projects/finance', './assets/js/dashboard/projects/projects/finance.js')
    .addEntry('dashboard/engineers/form', './assets/js/dashboard/engineers/form.js')
    .addEntry('dashboard/agents/form', './assets/js/dashboard/agents/form.js')
    .addEntry('dashboard/contractors/form', './assets/js/dashboard/contractors/form.js')
    .addEntry('dashboard/assets/assets/index', './assets/js/dashboard/assets/assets/index.js')
    .addEntry('dashboard/assets/assets/form', './assets/js/dashboard/assets/assets/form.js')
    .addEntry('dashboard/assets/assets/finance', './assets/js/dashboard/assets/assets/finance.js')
    .addEntry('dashboard/investments/index', './assets/js/dashboard/investments/index.js')
    .addEntry('dashboard/investments/view', './assets/js/dashboard/investments/view.js')
    .addEntry('dashboard/configuration/companies/index', './assets/js/dashboard/configuration/companies/index.js')
    .addEntry('dashboard/configuration/companies/form', './assets/js/dashboard/configuration/companies/form.js')
    .addEntry('dashboard/configuration/locales/form', './assets/js/dashboard/configuration/locales/form.js')
    .addEntry('dashboard/mynetwork/agents/index', './assets/js/dashboard/mynetwork/agents/index.js')
    .addEntry('dashboard/mynetwork/investors/index', './assets/js/dashboard/mynetwork/investors/index.js')
    .addEntry('dashboard/mynetwork/tenants/index', './assets/js/dashboard/mynetwork/tenants/index.js')
    .addEntry('investor/projects', './assets/js/dashboard/investor/projects.js')
    .addEntry('investor/assets', './assets/js/dashboard/investor/assets.js')
    .addEntry('investor/view', './assets/js/dashboard/investor/view.js')
    .addEntry('executive-club-form', './assets/js/dashboard/executive-club-form.js')
    .addEntry('company-balance', './assets/js/dashboard/company-balance.js')
    .addEntry('user-balance', './assets/js/dashboard/user-balance.js')
    .addEntry('dashboard/myfinance/projects/index', './assets/js/dashboard/myfinance/projects/index.js')
    .addEntry('dashboard/myfinance/assets/index', './assets/js/dashboard/myfinance/assets/index.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    //.splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()
    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableVueLoader()
    
    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
