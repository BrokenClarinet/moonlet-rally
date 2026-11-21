"""Build the zip to upload to Poki: a single self-contained index.html (three.js inlined through an import map) at the zip root,
so nothing can go missing whatever the uploader does with folders."""
import base64, os, zipfile
root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
html = open(os.path.join(root, 'index.html'), encoding='utf-8').read()
assert "window.MR_PLATFORM = 'poki';" in html, 'index.html must be the Poki build'
imp = "import * as THREE from './vendor/three.module.min.js';"
assert html.count(imp) == 1
three = open(os.path.join(root, 'vendor', 'three.module.min.js'), 'rb').read()
importmap = '<script type="importmap">{"imports":{"three":"data:text/javascript;base64,' + base64.b64encode(three).decode() + '"}}</script>\n'
html = html.replace(imp, "import * as THREE from 'three';")
assert html.count('<script type="module">') == 1
html = html.replace('<script type="module">', importmap + '<script type="module">', 1)
os.makedirs(os.path.join(root, 'dist'), exist_ok=True)
out = os.path.join(root, 'dist', 'moonlet-rally-poki.zip')
with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as z:
    z.writestr('index.html', html)
    z.write(os.path.join(root, 'vendor', 'THREE-LICENSE.txt'), 'THREE-LICENSE.txt')
print(out, os.path.getsize(out), 'bytes')
