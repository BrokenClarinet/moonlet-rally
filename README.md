# Moonlet Rally

Drive a little car around dead moonlets. Your tire tracks bring the ground back to life, and restored planets grow gems for you, even while you're away.

Browser game built with [Three.js](https://threejs.org) r170 (vendored in `vendor/`, MIT license). No build step.

## Run locally

```sh
python3 -m http.server 8000
# open http://localhost:8000
```

## Controls

- Arrows / WASD: drive
- Space: drift
- Shift or E: nitro boost (charged by drifting, seed pods and smashing rocks)
- G: garage (car bodies, paint, wheels, toppers; some unlock with gems)
- Phones: on-screen buttons, auto-accelerate

## How it plays

- Tire tracks bring dead ground to life. Painting fresh ground builds an x2 to x5 combo.
- Seed pods (glowing beams) make a big bloom and refill nitro.
- Rocks bounce you off at low speed and smash at high speed or while boosting.
- Purple mushrooms launch you into the air. Long jumps bloom where you land.
- Restored ground grows gems over time, also while you're away (50%, capped at 2 hours).
- Restore enough of a moonlet (50% on the first, then 65% and 75%) and a launch ramp opens to fly you to the next one.

## Art and licensing

All visuals are generated in code from basic shapes (toon shading, ink outlines, faceted props, gradient sky). The UI icons are our own inline SVG. No third-party models, textures, fonts or icon sets are used. Three.js is MIT-licensed; see `vendor/THREE-LICENSE.txt`.
