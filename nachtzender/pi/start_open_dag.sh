#!/bin/bash
# Start de Nachtzender en het scorebord op volledig scherm.
# Automatisch starten bij inloggen: zie "Automatisch starten" in de README.
cd "$(dirname "$0")"
python3 nachtzender.py --posten /srv/nachtzender "$@" &
sleep 4
BROWSER=$(command -v chromium || command -v chromium-browser)
"$BROWSER" --kiosk --noerrdialogs --disable-infobars http://localhost:8080/ &
wait
