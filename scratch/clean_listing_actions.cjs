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
    // Skip users listing since it's already correct
    if (filePath.endsWith('users/listing.blade.php')) {
        return;
    }

    let content = fs.readFileSync(filePath, 'utf8');
    let modified = false;

    // Regex to match a <td> or similar container containing edit/delete action buttons
    // We will scan for anchor tags with edit/delete hrefs and replace them.
    
    // 1. Convert Edit buttons
    const editRegex = /<a href="([^"]*?(?:edit|show)[^"]*?)" class="btn[^"]*?">[\s\S]*?<i class="[^"]*?fa-edit[^"]*?"><\/i>[\s\S]*?<\/a>/gi;
    if (editRegex.test(content)) {
        content = content.replace(editRegex, '<a href="$1" class="nexora-action-btn is-warning" title="Edit"><i class="fas fa-edit"></i></a>');
        modified = true;
    }

    // 2. Convert Delete buttons
    const deleteRegex = /<a href="([^"]*?(?:delete|destroy)[^"]*?)" class="btn[^"]*?">[\s\S]*?<i class="[^"]*?fa-trash[^"]*?"><\/i>[\s\S]*?<\/a>/gi;
    if (deleteRegex.test(content)) {
        content = content.replace(deleteRegex, '<a href="$1" class="nexora-action-btn is-danger delete-category" title="Delete"><i class="fas fa-trash"></i></a>');
        modified = true;
    }

    // 3. Wrap action buttons inside nexora-action-group if they are adjacent
    // Match any <td> containing nexora-action-btn elements, and wrap the elements inside nexora-action-group if not already wrapped
    const tdActionRegex = /<td([^>]*)>\s*(<a class="nexora-action-btn[\s\S]*?<\/a>)\s*(<a class="nexora-action-btn[\s\S]*?<\/a>)\s*<\/td>/gi;
    if (tdActionRegex.test(content)) {
        content = content.replace(tdActionRegex, '<td$1>\n                                    <div class="nexora-action-group">\n                                        $2\n                                        $3\n                                    </div>\n                                </td>');
        modified = true;
    }

    if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Updated actions: ${path.relative(viewsDir, filePath)}`);
    }
});

console.log('All listing actions updated.');
