function appConsole(type, debug, ...consoleArgs) {
    if (debug) {
        console[type ?? 'log'].apply(this, consoleArgs);
    }
}

export {
    appConsole,
};
