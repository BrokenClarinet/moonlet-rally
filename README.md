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
- Shift: drift (charges nitro)
- E or Q: nitro boost (charged by drifting, seed pods and smashing rocks)
- C or Ctrl (hold): precision mode, slower with tight steering for lining up the ramp
- Space: jump (hold to charge a higher jump)
- In the air: Up/Down to flip, Left/Right to spin
- G: garage (car bodies, paint, wheels, toppers; some unlock with gems)
- H: Gem Garden (idle area to harvest and upgrade gem silos)
- Esc: close menus
- Phones: on-screen buttons, auto-accelerate; hold JUMP in the air to flip

## How it plays

- Tire tracks bring dead ground to life. Painting fresh ground builds an x2 to x5 combo.
- Seed pods (glowing beams) make a big bloom and refill nitro.
- Rocks bounce you off at low speed and smash at high speed or while boosting.
- Purple mushrooms and charged jumps launch you into the air. Flips and spins score gems and nitro, and chaining tricks builds a combo. Land crooked and it's sloppy; land on your roof and you crash.
- Gem trails arc over mushrooms and hops. Grab a whole trail for a bonus.
- Bloom Rush: every so often, fresh ground pays triple gems for 15 seconds.
- An arrow points to the launch ramp (or next hoop), and the car gently steers into the ramp when you're close.
- Restore enough of a moonlet and a launch ramp opens to fly you to the next one. Each moonlet has its own twist:
  - Meadow: the basics.
  - Frostlet: frozen ground is slippery until it blooms.
  - Geyser Moon: vents erupt on a timer and fling you skyward.
  - Gloom Moon: low gravity, and wisps that eat your grass. Ram them.
  - Breezy Moon: wind gusts push the car around, even more in the air.
  - Hoop Moon: drive through chains of golden hoops.
  - Starfall Moon: catch falling star-seeds for big blooms.
  - After seven moonlets the loop repeats with bigger, richer moonlets.
- Gem Garden: every restored moonlet becomes a silo that grows gems (also while you're away, up to 8 hours, each silo holds 2 hours). Harvest, upgrade silos and buy sprinklers there.
- Garage parts change how the car plays (bodies, wheels and toppers each have a perk; paint is cosmetic).

## Platform (Poki)

Add `?pokidebug` to the URL to turn on Poki debug mode. `window.MR_PLATFORM` in `index.html` is `'poki'` for the Poki build and `'none'` elsewhere. With Poki, the game loads the Poki SDK and calls `gameLoadingFinished`, `gameplayStart`/`gameplayStop`, a `commercialBreak` between moonlets and when leaving the garage or Gem Garden, and opt-in `rewardedBreak`s in the garage (gems) and Gem Garden (+30 min of growth). Progress is saved to `localStorage` (key `moonletRally.v1`, well under Poki's 1 MB limit), which Poki syncs to the cloud automatically for logged-in players. The title screen offers a Poki login button to players who aren't logged in. Without the SDK every call is a no-op.

## Art and licensing

All visuals are generated in code from basic shapes (toon shading, ink outlines, faceted props, gradient sky). The UI icons are our own inline SVG. No third-party models, textures, fonts or icon sets are used. Three.js is MIT-licensed; see `vendor/THREE-LICENSE.txt`.
