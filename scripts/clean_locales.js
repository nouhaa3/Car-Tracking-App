const fs = require('fs');
const path = require('path');
const files = [
  'resources/js/locales/fr.js',
  'resources/js/locales/en.js',
  'resources/js/locales/ar.js',
];

for (const f of files) {
  const full = path.resolve(f);
  const bak = full + '.bak';
  try {
    if (!fs.existsSync(bak)) fs.copyFileSync(full, bak);
    let s = fs.readFileSync(full, 'utf8');
    // remove BOM
    s = s.replace(/^\uFEFF/, '');
    // remove uncommon control chars that break JS parsing
    s = s.replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F\u2028\u2029]/g, '');
    // escape @ inside single-quoted strings only: '...@...'
    s = s.replace(/'([^']*?)@([^']*?)'/g, (m, p1, p2) => `'${p1}{'@'}${p2}'`);
    // also escape occurrences in double-quoted strings
    s = s.replace(/"([^\"]*?)@([^\"]*?)"/g, (m, p1, p2) => `"${p1}{'@'}${p2}"`);
    fs.writeFileSync(full, s, 'utf8');
    console.log('cleaned', f);
  } catch (err) {
    console.error('error cleaning', f, err.message);
    process.exit(1);
  }
}
console.log('done');
