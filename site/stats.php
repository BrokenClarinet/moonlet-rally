<?php
// The playtest stats panel was removed (user request, 2026-11-20). This stub overwrites the old page on the host,
// since the deploy upload never deletes files. Logged data stays in private/data, which is not web-readable.
http_response_code(404);
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');
exit;
