# Moonlet Rally

Drive a little car around dead moonlets. Your tire tracks bring the ground back to life, and restored planets grow gems for you, even while you're away.

Browser game built with [Three.js](https://threejs.org) r170 (vendored in `vendor/`, MIT license). No build step.

## Run locally

```sh
python3 -m http.server 8000
# open http://localhost:8000
```

## Controls

All keys can be changed in Controls (main menu or pause menu). Defaults:

- Arrows / WASD: drive
- Shift: drift (charges nitro)
- E or Q: nitro boost (charged by drifting, seed pods and smashing rocks)
- C or Ctrl (hold): precision mode, slower with tight steering for lining up the ramp
- Space: jump, instantly (keep holding for a higher hop)
- In the air: Up/Down to flip, Left/Right to spin
- G: garage (car bodies, paint, wheels, toppers; some unlock with gems)
- H: Gem Garden (idle area to harvest and upgrade gem silos)
- M: Moonlets map (fly back to any moonlet you've unlocked)
- P or Esc: pause (Esc also closes menus)
- Phones and tablets: the car drives itself forward. Drag the left thumb stick to steer, pull it back to brake or reverse; in the air push it up/down to flip and sideways to spin. Right side: JUMP, BOOST, DRIFT and SLOW (precision).

## How it plays

- Tire tracks bring dead ground to life. Painting fresh ground builds an x2 to x5 combo.
- Seed pods (glowing beams) make a big bloom and refill nitro.
- Rocks bounce you off at low speed and smash at high speed or while boosting.
- Purple mushrooms and charged jumps launch you into the air. Flips and spins score gems and nitro; bigger tricks pay more per rotation (a triple flip pays six times a single), and chaining tricks builds a combo. Land crooked and it's sloppy; land on your roof and you crash.
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
  - Moonlets VIII to XIII add Jelly Moon (bouncy ground: flip between bounces), Teacup, Blizzard (ice + wind), Ember (geysers + wisps), Comet (stars + hoops) and Trampoline Moon.
  - Moonlets XIV to XX bring three new twists: speed pads (Zoom Moon), warp portals (Warp Moon) and meteor showers that leave space crystals (Meteor Moon), then mix them in Rally, Haunted, Crater and Neon Moon.
  - After that, moonlets are generated forever (later ones can mix three twists): each gets its own name, colors, size, gravity and a mix of one or two twists, and pays more gems than the last.
- Moonlets map: fly back to any unlocked moonlet. Each moonlet's grass is kept (localStorage `moonletRally.life.<n>`, 4 bits per point), and restoring more of an old moonlet raises its Gem Garden output.
- Gem Garden: every restored moonlet becomes a silo that grows gems (also while you're away, up to 8 hours, each silo holds 2 hours). Harvest, upgrade silos and buy sprinklers there.
- Garage Upgrades tab: ten levelled upgrades bought with gems (wider tires, engine, tread, nitro tank, springs, trick wax, gem magnet, bull bar, gem polish, combo keeper).
- Garage: 6 bodies, 14 paints, 6 wheel sets, 9 toppers and 5 drift/boost trails. Bodies, wheels and toppers each have a perk; paint and trails are cosmetic.
- Garage parts change how the car plays (bodies, wheels and toppers each have a perk; paint is cosmetic).

## Menus and settings

A loading bar runs while the moonlet is built, then the main menu offers Play/Continue, Settings and Controls. Settings (graphics quality, ink outlines, camera shake, speed zoom, camera distance, guide arrow, touch buttons, FPS counter, reset progress) are saved under `moonletRally.settings` and key bindings under `moonletRally.keys`. The game pauses itself when the tab is hidden.

## Website (password-protected test build)

`site/` is a small PHP website: a public info page (`index.php`) with a password box, and the game behind it (`play.php`). The game file is stored in `private/` and only loads after login, so the password can't be read from the page source.

- Build: `python3 tools/build_site.py` creates `dist/site/` and `dist/moonlet-rally-site.zip`.
- Manual upload: unzip onto any host with PHP 7.4+ (Apache hosts such as IONOS also honor the included `.htaccess` files). Edit `secret.php` on the server and put your password between the quotes. Optionally set `$contact` in `index.php` to show a support email.
- GitHub deploy: add repository secrets `FTP_SERVER` (host name, e.g. `ftp.example.com`),, `FTP_USERNAME`, `FTP_PASSWORD` and `SITE_PASSWORD` (optionally variable `FTP_DIR`), then run **Actions > Deploy website**. It builds from the repo and uploads with SFTP, falling back to FTP (encrypted when the host supports it), and writes `secret.php` from `SITE_PASSWORD`. A repository variable `FTP_PROTOCOL` (`sftp` or `ftp`) forces one.
- The site build turns the Poki SDK off (`MR_PLATFORM = 'none'`) and has no ads.

## Platform (Poki)

Add `?pokidebug` to the URL to turn on Poki debug mode. `window.MR_PLATFORM` in `index.html` is `'poki'` for the Poki build and `'none'` elsewhere. With Poki, the game loads the Poki SDK and calls `gameLoadingFinished`, `gameplayStart`/`gameplayStop`, a `commercialBreak` between moonlets and when leaving the garage or Gem Garden, and opt-in `rewardedBreak`s in the garage (gems) and Gem Garden (+30 min of growth). Progress is saved to `localStorage` (key `moonletRally.v1`, well under Poki's 1 MB limit), which Poki syncs to the cloud automatically for logged-in players. The title screen offers a Poki login button to players who aren't logged in. Without the SDK every call is a no-op.

## Art and licensing

All visuals are generated in code from basic shapes (toon shading, ink outlines, faceted props, gradient sky). The UI icons are our own inline SVG. No third-party models, textures, fonts or icon sets are used. Three.js is MIT-licensed; see `vendor/THREE-LICENSE.txt`.

## Quality of life

- The guide arrow points to the nearest bare ground when you're driving on restored ground.
- The Moonlets map shows each moonlet's twists and a preview of the next one.
- Stats page in the pause menu (time, gems earned, best trick, longest air and more).
- Settings > Save code copies your progress as a short code you can load on another device or browser.
- Garage > Upgrades has a button that buys the cheapest upgrades you can afford.

## Saves, idle gems and ads

- Saves live in localStorage. On Poki the game waits for the SDK to finish loading (which restores a logged-in player's cloud save) before reading anything, and grass is run-length packed, so a full save is a few KB (Poki's cloud-save limit is 1MB gzipped).
- Gem Garden silos start at 30 minutes of growth and upgrade (Bigger silos) up to 12 hours. Full silos stop growing, so players have a reason to come back.
- Welcome-back card: collect what grew while you were away, or watch a rewarded ad to double it. After any harvest, a rewarded ad can double it too.
- Ads: commercial breaks before gameplay resumes (leaving the garage, garden, pause menu or main menu, and between moonlets); Poki decides whether one actually plays. Rewarded ads are always optional and labelled. `MR_PLATFORM = 'demo'` (used by the private website build) shows stand-in ad cards instead of real ads.

## Moon critters

Fluffy critters move onto the grass you grow; more appear as a moonlet fills up. Bumping one costs 5 to 10% of the gems you carry and breaks your combos (then 4 seconds of grace). Slow driving only nudges past them. Whizzing past one at speed without touching it pays a small bonus.
- Poki upload zip: `python3 tools/build_poki.py` writes dist/moonlet-rally-poki.zip (index.html at the root + vendor/three.js).

## Sound

Every sound is synthesized at runtime with the Web Audio API (engine, drift and boost hiss, jumps, tricks, gems, critters, twists, menus), so the game ships zero audio files. Audio starts on the first tap or key press, mutes during ads and when the tab is hidden, and has Sound and Volume settings.

## Prices

All prices (upgrades, car parts, sprinklers, silos) scale with the earning rate of the furthest moonlet reached (`econ()`), so they cost roughly the same amount of play time at every stage. Garden level-ups scale with that moonlet's own income.
