const fs = require('fs');
const files = ['resources/js/locales/fr.js','resources/js/locales/en.js','resources/js/locales/ar.js'];
for (const f of files) {
  try {
    let s = fs.readFileSync(f,'utf8');
    const orig = s;
    s = s.replace(/\{'@'\}/g, "{\\'@\\'}");
    if (s !== orig) {
      fs.writeFileSync(f, s, 'utf8');
      console.log('patched', f);
    }
  } catch (e) {
    console.error('error', f, e.message);
  }
}
console.log('done');
