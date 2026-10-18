"""Het tekstprotocol tussen de ESP32-knoppenkasten en de server (UDP).

ESP32 -> server
    HELLO <speler>                ongeveer elke seconde: "ik ben er nog"
    BTN <speler> <knop> <volgnr>  knop 0-3 ingedrukt; volgnr hoort bij één druk

server -> ESP32
    ACK <volgnr>                  druk ontvangen, niet opnieuw sturen
    LED <patroon>                 4 tekens: 0 = uit, 1 = aan, B = knipperen

De ESP32 stuurt een BTN opnieuw zolang er geen ACK komt. Dezelfde druk
heeft dan hetzelfde volgnummer, zodat de server hem maar één keer telt.
"""

from dataclasses import dataclass


@dataclass(frozen=True)
class Hello:
    speler: int


@dataclass(frozen=True)
class Knop:
    speler: int
    knop: int
    volgnr: int


def lees_bericht(data: bytes) -> Hello | Knop | None:
    """Zet een binnengekomen pakket om; None als het onzin is."""
    try:
        delen = data.decode("ascii").split()
        if delen[0] == "HELLO" and len(delen) == 2:
            return Hello(int(delen[1]))
        if delen[0] == "BTN" and len(delen) == 4:
            return Knop(int(delen[1]), int(delen[2]), int(delen[3]))
    except (UnicodeDecodeError, ValueError, IndexError):
        pass
    return None


def ack(volgnr: int) -> bytes:
    return f"ACK {volgnr}".encode("ascii")


def led(patroon: str) -> bytes:
    return f"LED {patroon}".encode("ascii")
