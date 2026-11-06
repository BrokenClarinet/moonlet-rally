"""Build the zip to upload to Poki (Poki Inspector / Poki for Developers): index.html at the root, three.js vendored next to it."""
import os, zipfile
root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
html = open(os.path.join(root, 'index.html'), encoding='utf-8').read()
assert "window.MR_PLATFORM = 'poki';" in html, 'index.html must be the Poki build'
os.makedirs(os.path.join(root, 'dist'), exist_ok=True)
out = os.path.join(root, 'dist', 'moonlet-rally-poki.zip')
with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as z:
    z.writestr('index.html', html)
    for f in ('three.module.min.js', 'THREE-LICENSE.txt'):
        z.write(os.path.join(root, 'vendor', f), 'vendor/' + f)
print(out, os.path.getsize(out), 'bytes')
