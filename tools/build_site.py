#!/usr/bin/env python3
"""Build the password-protected website into dist/site and dist/moonlet-rally-site.zip.

The game itself (index.html) is copied into site/private/game.php behind a PHP guard,
so it can only be loaded through play.php after logging in.
Set SITE_PASSWORD in the environment to write secret.php with that password
(used by the GitHub deploy workflow); otherwise secret.php holds a placeholder.
"""
import os, shutil, zipfile, pathlib

ROOT = pathlib.Path(__file__).resolve().parent.parent
SRC, DIST = ROOT / 'site', ROOT / 'dist'
OUT = DIST / 'site'

shutil.rmtree(OUT, ignore_errors=True)
shutil.copytree(SRC, OUT)

game = (ROOT / 'index.html').read_text()
assert '<?' not in game, 'game HTML must not contain PHP open tags'
game = game.replace("'./vendor/three.module.min.js'", "'./three.module.min.js'")
game = game.replace("window.MR_PLATFORM = 'poki';", "window.MR_PLATFORM = 'none';")
assert "'./three.module.min.js'" in game and "MR_PLATFORM = 'none'" in game
guard = "<?php if (!defined('MR_OK')) { http_response_code(403); exit; } ?>\n"
(OUT / 'private' / 'game.php').write_text(guard + game)

shutil.copy(ROOT / 'vendor' / 'three.module.min.js', OUT / 'three.module.min.js')
shutil.copy(ROOT / 'vendor' / 'THREE-LICENSE.txt', OUT / 'THREE-LICENSE.txt')

pw = os.environ.get('SITE_PASSWORD', '')
if pw:
    esc = pw.replace('\\', '\\\\').replace("'", "\\'")
    (OUT / 'secret.php').write_text(f"<?php\nreturn '{esc}';\n")
else:
    shutil.copy(OUT / 'secret.example.php', OUT / 'secret.php')

zp = DIST / 'moonlet-rally-site.zip'
zp.unlink(missing_ok=True)
with zipfile.ZipFile(zp, 'w', zipfile.ZIP_DEFLATED) as z:
    for f in sorted(OUT.rglob('*')):
        if f.is_file():
            z.write(f, f.relative_to(OUT))
print(f'built {OUT} and {zp} ({zp.stat().st_size // 1024} KB)')
