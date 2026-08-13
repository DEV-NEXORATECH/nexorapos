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
    // Skip already updated files to prevent double-processing
    if (filePath.endsWith('users/add.blade.php') || filePath.endsWith('users/update.blade.php') || filePath.endsWith('features/create.blade.php') || filePath.endsWith('features/show.blade.php')) {
        return;
    }

    // We only target add, edit, update, and create files
    const base = path.basename(filePath);
    if (!['add.blade.php', 'edit.blade.php', 'update.blade.php', 'create.blade.php'].includes(base)) {
        return;
    }

    let content = fs.readFileSync(filePath, 'utf8');
    let modified = false;

    // Determine status color class
    let colorClass = 'is-success'; // default green
    const lowPath = filePath.toLowerCase();
    if (lowPath.includes('tax') || lowPath.includes('discount') || lowPath.includes('setting')) {
        colorClass = 'is-warning'; // yellow
    } else if (lowPath.includes('product') || lowPath.includes('variant') || lowPath.includes('item') || lowPath.includes('stock') || lowPath.includes('role') || lowPath.includes('permission')) {
        colorClass = 'is-neutral'; // black
    } else if (lowPath.includes('customer') || lowPath.includes('supplier') || lowPath.includes('store') || lowPath.includes('category')) {
        colorClass = 'is-success'; // green
    }

    // 1. Replace the header (page-title or nexora-page-head)
    let headerReplaced = false;
    
    // Pattern A: nexora-page-head
    const pageHeadRegex = /<div class="nexora-page-head">[\s\S]*?<h4 class="page-title[^"]*">(.*?)<\/h4>[\s\S]*?<\/div>/i;
    if (pageHeadRegex.test(content)) {
        content = content.replace(pageHeadRegex, (match, title) => {
            return `            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> ${title.trim()}</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>`;
        });
        headerReplaced = true;
        modified = true;
    }
    
    // Pattern B: Raw h4 page-title
    const pageTitleRegex = /<h4 class="page-title">(.*?)<\/h4>/i;
    if (!headerReplaced && pageTitleRegex.test(content)) {
        content = content.replace(pageTitleRegex, (match, title) => {
            return `            <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> ${title.trim()}</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>`;
        });
        headerReplaced = true;
        modified = true;
    }

    // 2. Convert standard cards to nexora-control-cards (Robust regex)
    const cardHeaderRegex = /<div class="card(?:\s+[^"]*)?">\s*<div class="card-header">[\s\S]*?<div class="card-title">(.*?)<\/div>[\s\S]*?<\/div>([\s\S]*?)<div class="card-body">/gi;
    if (cardHeaderRegex.test(content)) {
        content = content.replace(cardHeaderRegex, (match, title, intermediate) => {
            return `<div class="nexora-control-card ${colorClass} mb-4">
                    <h5><i class="fas fa-folder"></i> ${title.trim()}</h5>
                    ${intermediate}                    <div class="card-body p-0 mt-3">`;
        });
        modified = true;
    }

    // 3. Replace card action or footer
    const cardActionRegex = /<div class="(?:card-action|card-footer)(?:\s+[^"]*)?">/gi;
    if (cardActionRegex.test(content)) {
        content = content.replace(cardActionRegex, '<div class="nexora-form-actions mt-3">');
        modified = true;
    }

    // 4. Update submit buttons inside actions to btn-primary
    const submitBtnRegex = /<button class="btn btn-(?:success|info|default)"/gi;
    if (submitBtnRegex.test(content)) {
        content = content.replace(submitBtnRegex, '<button class="btn btn-primary"');
        modified = true;
    }

    if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Processed form layout: ${path.relative(viewsDir, filePath)}`);
    }
});

console.log('Form layouts cleanup finished.');
