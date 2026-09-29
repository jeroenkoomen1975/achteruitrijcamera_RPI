"""Hardware-onafhankelijke logica: zones bepalen, piepritme en displaytekst.

Deze module gebruikt geen GPIO, zodat hij ook op een gewone pc getest kan worden.
"""

from enum import Enum


class Zone(Enum):
    GROEN = "groen"
    ORANJE = "oranje"
    ROOD = "rood"


def bepaal_zone(afstand_cm, oranje_grens_cm, rood_grens_cm):
    """Geef de zone terug voor een gemeten afstand (None = niets gemeten)."""
    if afstand_cm is None or afstand_cm > oranje_grens_cm:
        return Zone.GROEN
    if afstand_cm > rood_grens_cm:
        return Zone.ORANJE
    return Zone.ROOD


def piep_pauze(afstand_cm, oranje_grens_cm, rood_grens_cm, piep_start_cm):
    """Pauze in seconden tussen twee piepjes.

    - None: niet piepen (niks dichtbij)
    - 0:    continu piepen (rode zone)
    - anders: hoe dichterbij, hoe korter de pauze
    """
    zone = bepaal_zone(afstand_cm, oranje_grens_cm, rood_grens_cm)
    if zone is Zone.ROOD:
        return 0.0
    if zone is Zone.ORANJE:
        # lineair van 0.25 s (op de oranje grens) naar 0.05 s (bij rood)
        verhouding = (afstand_cm - rood_grens_cm) / (oranje_grens_cm - rood_grens_cm)
        return 0.05 + 0.20 * verhouding
    # groene zone
    if afstand_cm is None or afstand_cm > piep_start_cm or piep_start_cm <= oranje_grens_cm:
        return None
    # lineair van 0.8 s (op piep_start) naar 0.3 s (op de oranje grens)
    verhouding = (afstand_cm - oranje_grens_cm) / (piep_start_cm - oranje_grens_cm)
    return 0.3 + 0.5 * verhouding


def mediaan(waarden):
    """Mediaan van een lijst getallen; None-waarden worden genegeerd."""
    geldig = sorted(w for w in waarden if w is not None)
    if not geldig:
        return None
    midden = len(geldig) // 2
    if len(geldig) % 2:
        return geldig[midden]
    return (geldig[midden - 1] + geldig[midden]) / 2


def display_regels(afstand_cm, zone, max_afstand_cm, breedte=16):
    """Maak de twee regels tekst voor het 16x2 LCD-display."""
    if afstand_cm is None or afstand_cm >= max_afstand_cm:
        regel1 = f"Afstand: >{max_afstand_cm:.0f}cm"
    else:
        regel1 = f"Afstand:{afstand_cm:5.1f} cm"

    teksten = {
        Zone.GROEN: "VRIJ",
        Zone.ORANJE: "LET OP",
        Zone.ROOD: "STOP!",
    }
    regel2 = teksten[zone]
    return regel1[:breedte].ljust(breedte), regel2.center(breedte)[:breedte]
