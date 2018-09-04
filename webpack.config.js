const path = require('path');

module.exports = {
    entry: './resources/assets/js/key-generator.js',
    node: {
        fs: "empty"
    },
    output: {
        filename: 'key-generator.js',
        path: path.resolve(__dirname, 'public/js')
    }
};
