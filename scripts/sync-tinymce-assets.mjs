import { cpSync, existsSync, mkdirSync, rmSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const repoRoot = resolve(__dirname, '..');
const source = resolve(repoRoot, 'node_modules', 'tinymce');
const target = resolve(repoRoot, 'public', 'vendor', 'tinymce');

if (!existsSync(source)) {
    console.warn('[sync-tinymce-assets] source missing:', source);
    process.exit(0);
}

mkdirSync(dirname(target), { recursive: true });
rmSync(target, { recursive: true, force: true });
cpSync(source, target, { recursive: true });

console.log('[sync-tinymce-assets] copied TinyMCE assets to', target);
