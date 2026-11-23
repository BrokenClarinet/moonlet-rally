"""Build the zip to upload to CrazyGames: one self-contained index.html (three.js inlined through an import map) at the
zip root, with MR_PLATFORM switched to 'crazygames' (CrazyGames HTML5 SDK v3: ads, gameplay events, Data module saves).
index.html in the repo stays the Poki build; this script only changes the copy that goes in the zip."""
import base64, os, zipfile
root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
html = open(os.path.join(root, 'index.html'), encoding='utf-8').read()
assert "window.MR_PLATFORM = 'poki';" in html, 'index.html must be the Poki build'
html = html.replace("window.MR_PLATFORM = 'poki';", "window.MR_PLATFORM = 'crazygames';", 1)
html = html.replace("'https://game-cdn.poki.com/scripts/v2/poki-sdk.js'", "''")  # the Poki branch never runs here; keep other portals' URLs out
imp = "import * as THREE from './vendor/three.module.min.js';"
assert html.count(imp) == 1
three = open(os.path.join(root, 'vendor', 'three.module.min.js'), 'rb').read()
importmap = '<script type="importmap">{"imports":{"three":"data:text/javascript;base64,' + base64.b64encode(three).decode() + '"}}</script>\n'
html = html.replace(imp, "import * as THREE from 'three';")
assert html.count('<script type="module">') == 1
html = html.replace('<script type="module">', importmap + '<script type="module">', 1)
assert 'poki.com' not in html and 'crazygames-sdk-v3.js' in html
os.makedirs(os.path.join(root, 'dist'), exist_ok=True)
with open(os.path.join(root, 'dist', 'crazygames-index.html'), 'w', encoding='utf-8') as f: f.write(html)
out = os.path.join(root, 'dist', 'moonlet-rally-crazygames.zip')
with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as z:
    z.writestr('index.html', html)
    z.write(os.path.join(root, 'vendor', 'THREE-LICENSE.txt'), 'THREE-LICENSE.txt')
print(out, os.path.getsize(out), 'bytes')
