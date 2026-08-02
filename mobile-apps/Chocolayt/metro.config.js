const { getDefaultConfig } = require('expo/metro-config');

const config = getDefaultConfig(__dirname);

// Restrict Metro to ONLY watch this folder, completely ignoring the massive Laravel app outside it
config.watchFolders = [__dirname];
config.resolver.disableHierarchicalLookup = true;

module.exports = config;
