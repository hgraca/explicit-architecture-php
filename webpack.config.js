var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .autoProvideVariables({
        "window.Bloodhound": require.resolve('bloodhound-js'),
        "jQuery.tagsinput": "bootstrap-tagsinput"
    })
    .enableSassLoader()
    .enableVersioning(false)
    .createSharedEntry('js/common', ['jquery'])
    .addEntry('js/app', './src/Presentation/Web/app.js')
    .addEntry('js/login', './src/Presentation/Web/Component/Login/Anonymous/login.js')
    .addEntry('js/admin', './src/Presentation/Web/Component/admin.js')
    .addEntry('js/search', './src/Presentation/Web/Component/Blog/Anonymous/search.js')
    .addStyleEntry('css/app', ['./src/Presentation/Web/app.scss'])
    .addStyleEntry('css/admin', ['./src/Presentation/Web/Component/admin.scss'])
;

module.exports = Encore.getWebpackConfig();
