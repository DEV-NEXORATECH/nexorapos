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

    // Check if the file contains the superadmin-header-card with inline style
    if (content.includes('superadmin-header-card')) {
        // 1. Remove style="align-items: center;" from header card
        content = content.replace(/class="superadmin-header-card mb-4" style="align-items: center;"/g, 'class="superadmin-header-card mb-4"');
        
        // 2. Remove styling from h4 inside the card
        content = content.replace(/<h4 class="mt-1 mb-2" style="color: #fff;">/g, '<h4>');
        content = content.replace(/<h4 class="mt-1 mb-2" style="color: #FFFFFF !important;">/g, '<h4>');
        
        // 3. Replace the button classes and styling
        content = content.replace(/class="btn" style="background-color: #FFFFFF !important; color: #0058A6 !important; border: none !important; white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;"/g, 'class="btn btn-secondary" style="border: none;"');
        
        // 4. Remove "+ " from button text (e.g. "+ Add New Category" -> "Add New Category")
        content = content.replace(/>\s*\+\s*Add New/g, '>Add New');
        
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Processed: ${path.relative(viewsDir, filePath)}`);
    }
});

console.log('All listing header formats updated.');
