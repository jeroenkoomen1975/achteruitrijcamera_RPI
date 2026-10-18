#!/bin/bash
# Opent het quizscherm schermvullend in Chromium (kioskmodus).
# Wordt automatisch gestart na het inloggen op het bureaublad (zie README).

URL="http://localhost:8080"

# Wacht tot de quizserver draait (maximaal 60 seconden).
for _ in $(seq 60); do
  curl -s -o /dev/null "$URL" && break
  sleep 1
done

# Raspberry Pi OS Bookworm heet het 'chromium-browser', Trixie 'chromium'.
BROWSER=$(command -v chromium-browser || command -v chromium)

exec "$BROWSER" \
  --kiosk \
  --noerrdialogs \
  --disable-infobars \
  --no-first-run \
  --disable-session-crashed-bubble \
  --disable-features=Translate \
  --autoplay-policy=no-user-gesture-required \
  --password-store=basic \
  "$URL"
