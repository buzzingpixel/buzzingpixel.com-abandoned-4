import fs from 'fs-extra';
import out from 'cli-output';
import path from 'path';
import recursive from 'recursive-readdir-sync';
import uglify from 'terser';

const appDir = process.cwd();
const jsLocation = `${appDir}/assets/js`;
const jsOutputDir = `${appDir}/public/assets/js`;
const options = {
    sourceMap: true,
    mangle: true,
    compress: true,
    output: {
        beautify: false,
    },
};

// Process a single source JS file
export function processSourceFile (filePath) {
    const ext = path.extname(filePath);
    const outputFullPath = jsOutputDir + filePath.slice(
        filePath.indexOf(jsLocation) + jsLocation.length,
    );
    const outputFullPathDir = path.dirname(outputFullPath);
    const sourceMapFullPath = `${outputFullPath}.map`;
    const sourceMapName = path.basename(sourceMapFullPath);
    const relativeReplacer = appDir + path.sep;
    const relativeName = filePath
        .slice(filePath.indexOf(relativeReplacer) + relativeReplacer.length)
        .split(path.sep)
        .join('/');
    const code = {};

    // If the file no longer exists at the source, delete it
    if (!fs.existsSync(filePath)) {
        if (fs.existsSync(sourceMapFullPath)) {
            fs.removeSync(sourceMapFullPath);
        }

        if (fs.existsSync(outputFullPath)) {
            fs.removeSync(outputFullPath);
        }

        return;
    }

    // If the file extension is not js, we should ignore it
    if (ext !== '.js') {
        return;
    }

    code[relativeName] = String(fs.readFileSync(filePath));

    const processed = uglify.minify(code, options);

    if (processed.error) {
        out.error('There was an error compiling Javascript');
        out.error(`Error: ${processed.error.message}`);
        out.error(`File: ${filePath}`);
        out.error(`Line: ${processed.error.line}`);
        out.error(`Column: ${processed.error.col}`);
        out.error(`Position: ${processed.error.pos}`);

        return;
    }

    // Create directory if it doesn't exist
    if (!fs.existsSync(outputFullPathDir)) {
        fs.mkdirSync(outputFullPathDir, { recursive: true });
    }

    // Write the sourcemap to disk
    fs.writeFileSync(sourceMapFullPath, processed.map);

    // Create the sourcemap code tag
    const sourceMapCode = `\n//# sourceMappingURL=${sourceMapName}`;

    // Write the JS file to disk
    fs.writeFileSync(outputFullPath, processed.code + sourceMapCode);
}

export default () => {
    // Let the user know what we're doing
    out.info('Compiling JS...');

    // Empty the JS dir first
    fs.emptyDirSync(jsOutputDir);

    // Add the gitignore file back
    fs.writeFileSync(
        `${jsOutputDir}/.gitignore`,
        '*\n!.gitignore',
    );

    // Recursively iterate through the files in the JS location
    recursive(jsLocation).forEach((filePath) => {
        // Send the file for processing
        processSourceFile(filePath);
    });

    out.success('JS compiled');
};
