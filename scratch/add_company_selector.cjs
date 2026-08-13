/**
 * This script:
 * 1. Adds @include('admin.partials.company-selector') to all POS Master add/edit blade files
 * 2. Updates all POS Service files to resolve company_id from request for super admin
 */
const fs = require('fs');
const path = require('path');

const viewsDir = 'resources/views/admin/pos';
const servicesDir = 'app/Services/Pos';

// ── STEP 1: Add company-selector partial to all add/update blade views ──
const posSubdirs = ['store', 'category', 'product', 'product-variant', 'tax', 'supplier', 'customer', 'discount', 'stock-movement', 'payment', 'method', 'purchase'];

function findBladeFiles(dir) {
    const results = [];
    if (!fs.existsSync(dir)) return results;
    for (const f of fs.readdirSync(dir)) {
        const fp = path.join(dir, f);
        const stat = fs.statSync(fp);
        if (stat.isDirectory()) {
            results.push(...findBladeFiles(fp));
        } else if (/\.(add|create|update|edit)\.blade\.php$/.test(f) || (f === 'add.blade.php' || f === 'update.blade.php')) {
            results.push(fp);
        }
    }
    return results;
}

const bladeFiles = findBladeFiles(viewsDir);
let bladeCount = 0;

for (const file of bladeFiles) {
    let content = fs.readFileSync(file, 'utf8');
    
    // Skip if already has company-selector
    if (content.includes('company-selector')) {
        console.log(`SKIP (already has): ${file}`);
        continue;
    }
    
    // Find the @csrf tag and insert company-selector right after the first row div
    // Strategy: insert after @csrf line
    const csrfIndex = content.indexOf('@csrf');
    if (csrfIndex === -1) {
        console.log(`SKIP (no @csrf): ${file}`);
        continue;
    }
    
    // Find the end of the @csrf line
    const afterCsrf = content.indexOf('\n', csrfIndex);
    if (afterCsrf === -1) {
        console.log(`SKIP (no newline after @csrf): ${file}`);
        continue;
    }
    
    // Insert company selector partial after @csrf
    const insertion = `\n                                        <div class="row">\n                                            @include('admin.partials.company-selector')\n                                        </div>\n`;
    content = content.slice(0, afterCsrf + 1) + insertion + content.slice(afterCsrf + 1);
    
    fs.writeFileSync(file, content, 'utf8');
    bladeCount++;
    console.log(`UPDATED: ${file}`);
}

console.log(`\n✅ Updated ${bladeCount} blade files with company-selector.\n`);

// ── STEP 2: Update all POS services to use request company_id for super admin ──
const serviceFiles = fs.readdirSync(servicesDir).filter(f => f.endsWith('.php'));
let serviceCount = 0;

for (const file of serviceFiles) {
    const fp = path.join(servicesDir, file);
    let content = fs.readFileSync(fp, 'utf8');
    let modified = false;
    
    // Pattern 1: $model->company_id = auth()->user()?->company_id;
    // Replace with: $model->company_id = $request->company_id ?? auth()->user()?->company_id;
    const storePattern = /(\$\w+->company_id\s*=\s*)auth\(\)->user\(\)\?->company_id;/g;
    if (storePattern.test(content)) {
        content = content.replace(
            /(\$\w+->company_id\s*=\s*)auth\(\)->user\(\)\?->company_id;/g,
            '$1(auth()->user()?->isSuperAdmin() && $request->company_id) ? $request->company_id : auth()->user()?->company_id;'
        );
        modified = true;
    }
    
    // Pattern 2: ->where('company_id', auth()->user()?->company_id) in index/listing
    // For index(), make super admin see ALL records
    const indexWherePattern = /->where\('company_id',\s*auth\(\)->user\(\)\?->company_id\)->get\(\)/g;
    if (indexWherePattern.test(content)) {
        content = content.replace(
            /->where\('company_id',\s*auth\(\)->user\(\)\?->company_id\)->get\(\)/g,
            '->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where(\'company_id\', auth()->user()?->company_id))->get()'
        );
        modified = true;
    }
    
    // Pattern 3: ->where('company_id', auth()->user()?->company_id)->first() in edit/show/update/destroy
    const firstWherePattern = /->where\('company_id',\s*auth\(\)->user\(\)\?->company_id\)->first\(\)/g;
    if (firstWherePattern.test(content)) {
        content = content.replace(
            /->where\('company_id',\s*auth\(\)->user\(\)\?->company_id\)->first\(\)/g,
            '->when(!auth()->user()?->isSuperAdmin(), fn($q) => $q->where(\'company_id\', auth()->user()?->company_id))->first()'
        );
        modified = true;
    }
    
    if (modified) {
        fs.writeFileSync(fp, content, 'utf8');
        serviceCount++;
        console.log(`SERVICE UPDATED: ${file}`);
    } else {
        console.log(`SERVICE SKIP: ${file}`);
    }
}

console.log(`\n✅ Updated ${serviceCount} service files with super admin company_id logic.`);
