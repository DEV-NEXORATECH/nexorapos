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

    // Check if the file contains the old card layout to convert
    if (content.includes('<h4 class="page-title">') && content.includes('<div class="card-header d-flex justify-content-between align-items-center">')) {
        // This is the old card layout (like tax/listing and supplier/listing)
        
        let titleMatch = content.match(/<h4 class="page-title">(.*?)<\/h4>/);
        let buttonMatch = content.match(/<a href="(.*?)" class="btn btn-primary">(.*?)<\/a>/);
        
        if (titleMatch && buttonMatch) {
            const title = titleMatch[1];
            const link = buttonMatch[1];
            const btnText = buttonMatch[2];
            
            let icon = 'fa-list';
            if (title.toLowerCase().includes('tax')) icon = 'fa-percent';
            if (title.toLowerCase().includes('supplier')) icon = 'fa-truck';
            
            const newHeader = `            <div class="superadmin-header-card mb-4">
                <h4><i class="fas ${icon}"></i> ${title}</h4>
                <a href="${link}" class="btn btn-secondary" style="border: none;">${btnText}</a>
            </div>`;
            
            // Remove the wrapping page-title, row, col, card, card-header, card-body
            content = content.replace(/<h4 class="page-title">[\s\S]*?<div class="card-body">/g, newHeader);
            
            // Remove the closing divs at the bottom of the card
            // Find the </table> and replace the trailing closing divs after it
            content = content.replace(/<\/table>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/g, '</table>');
            
            fs.writeFileSync(filePath, content, 'utf8');
            console.log(`Converted Layout: ${path.relative(viewsDir, filePath)}`);
            modified = true;
        }
    }

    if (!modified && content.includes('superadmin-header-card')) {
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
