const fs = require('fs');
const path = require('path');

const walk = (dir) => {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach((file) => {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) {
            results = results.concat(walk(file));
        } else if (file.endsWith('.blade.php')) {
            results.push(file);
        }
    });
    return results;
};

const viewsDir = 'resources/views/admin';
const files = walk(viewsDir);

files.forEach((filePath) => {
    let content = fs.readFileSync(filePath, 'utf8');
    let modified = false;

    // 1. If we have nexora-action-btn on an edit link but it's missing is-warning, add it
    const editFixRegex = /<a href="([^"]*?(?:edit)[^"]*?)" class="nexora-action-btn"(?!\s+is-warning)/gi;
    if (editFixRegex.test(content)) {
        content = content.replace(editFixRegex, '<a href="$1" class="nexora-action-btn is-warning"');
        modified = true;
    }

    // 2. If we have nexora-action-btn on a show/view link but it's missing is-info, add it
    const showFixRegex = /<a href="([^"]*?(?:show|view)[^"]*?)" class="nexora-action-btn"(?!\s+is-info)/gi;
    if (showFixRegex.test(content)) {
        content = content.replace(showFixRegex, '<a href="$1" class="nexora-action-btn is-info"');
        modified = true;
    }

    if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Audited and Fixed: ${path.relative(viewsDir, filePath)}`);
    }
});

console.log('Audit completed.');
