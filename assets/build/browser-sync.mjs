import browsersync from 'browser-sync';

const bs = browsersync.create();

const appDir = process.cwd();
const cssOutputFile = `${appDir}/public/assets/css/style.min.css`;
const jsOutputDir = `${appDir}/public/assets/js//**`;
const srcDir = `${appDir}/src//**`;
const templatesDir = `${appDir}/assets/templates//**`;

const watchFiles = [
    cssOutputFile,
    jsOutputDir,
    srcDir,
    templatesDir,
    '!*.diff',
    '!*.err',
    '!*.log',
    '!*.orig',
    '!*.rej',
    '!*.swo',
    '!*.swp',
    '!*.vi',
    '!*.cache',
    '!*.DS_Store',
    '!*.tmp',
    '!*error_log',
    '!*Thumbs.db',
];

export default () => {
    bs.init({
        files: watchFiles,
        ghostMode: false,
        injectChanges: true,
        notify: false,
        proxy: 'https://buzzingpixel.localtest.me:26087/',
        reloadDelay: 100,
        reloadDebounce: 100,
        reloadThrottle: 1000,
        watchOptions: {
            ignored: '.DS_Store',
            usePolling: false,
        },
    });
};
