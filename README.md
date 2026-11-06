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
- Restore enough of a moonlet and a launch ramp opens to fly you to the next one. Each moonlet has its own twist:
  - Meadow: the basics.
  - Frostlet: frozen ground is slippery until it blooms.
  - Gloom Moon: low gravity, and wisps that eat your grass. Ram them.
  - Hoop Moon: drive through chains of golden hoops.
- Garage parts change how the car plays (bodies, wheels and toppers each have a perk; paint is cosmetic).

## Platform (Poki)

Add `?pokidebug` to the URL to turn on Poki debug mode. `window.MR_PLATFORM` in `index.html` is `'poki'` for the Poki build and `'none'` elsewhere. With Poki, the game loads the Poki SDK and calls `gameLoadingFinished`, `gameplayStart`/`gameplayStop`, a `commercialBreak` between moonlets and when leaving the garage, and an opt-in `rewardedBreak` in the garage for gems. Progress is saved to `localStorage` (key `moonletRally.v1`, well under Poki's 1 MB limit), which Poki syncs to the cloud automatically for logged-in players. The title screen offers a Poki login button to players who aren't logged in. Without the SDK every call is a no-op.

## Art and licensing

All visuals are generated in code from basic shapes (toon shading, ink outlines, faceted props, gradient sky). The UI icons are our own inline SVG. No third-party models, textures, fonts or icon sets are used. Three.js is MIT-licensed; see `vendor/THREE-LICENSE.txt`.
