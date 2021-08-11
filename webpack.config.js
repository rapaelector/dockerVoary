const Encore = require('@symfony/webpack-encore');

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
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')

    /**
     * Entry for role
     */
    .addEntry('app_user_role', './assets/js/role/user_roles.js')

    /***
     * Entry for client
     * - For client collection type
     */
    .addEntry('app_client', './assets/js/client/app.js')

    /**
     * Entry for project case but in the same folder of project
     */
    .addEntry('app_prospect_list', './assets/js/client/list.js')

    /***
     * Entry for project
     * - For client collection type
     */
    .addEntry('app_project', './assets/js/project/app.js')

    /**
     * Entry for project case but in the same folder of project
     */
    .addEntry('app_case_list', './assets/js/project/case_list.js')

    /**
     * Entry for project case in list project
     */
    .addEntry('app_project_list', './assets/js/project/list.js')

    /***
     * Entry for entry for new project
     * - For client collection type
     */
    .addEntry('app_project_new', './assets/js/project/new.js')

    /**
     * Entry for pdf page
     */
    .addStyleEntry('app_pdf', './assets/scss/pdf/app.scss')

    /**
     * Entry for angularJS
     * - Try to make it global
     */
    .addEntry('app_dashboard', './assets/js/dashboard/app.js')

    /**
     * Entry for project_ng
     * Project make by angular
     */
    .addEntry('ng_project', './assets/js/ng-project/app.js')

    /**
     * Project schedule app
     */
    .addEntry('app_project_schedule', './assets/js/project-schedule/app.js')
    
    /**
     * Plan de charge economiste
     * 
     * Load plan app
     */
    .addEntry('load_plan_app', './assets/js/load-plan/app.js')

    /**
     * Plan de charge economiste planning
     * Load plan planning app
     */
    .addEntry('load_plan_planning_app', './assets/js/load-plan-planning/app.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

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

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    // .enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery();

    Encore.addLoader({ test: /\.html$/i, loader: 'html-loader' });

var config = Encore.getWebpackConfig();

config.resolve.alias = {
    ...(config.resolve.alias ? config.resolve.alias : {}),
    'jquery': require.resolve('jquery'),
};

module.exports = config;