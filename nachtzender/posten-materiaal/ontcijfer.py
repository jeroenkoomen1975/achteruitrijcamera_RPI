# ===================== POST 5: PROGRAMMEREN (gevorderd) =====================
#
# De Nachtzender versleutelt zijn bericht met een "one-time pad":
#
#     versleuteld cijfer = (bericht-cijfer + sleutel-cijfer) % 10
#
# Voorbeeld: bericht 3, sleutel 9  ->  (3 + 9) % 10 = 12 % 10 = 2
#
# Vul hieronder de 4 groepen van de zender en de 4 sleutelgroepen van de
# posten in, maak de twee functies af en druk op Run (F5).

bericht = ["00000", "00000", "00000", "00000"]   # de 4 groepen van de zender
sleutel = ["00000", "00000", "00000", "00000"]   # sleutelgroep 1, 2, 3 en 4


def ontcijfer(versleuteld, sleutel):
    """Draai de versleuteling terug voor één cijfer."""
    # TODO: het tegenovergestelde van optellen is ...
    # Tip: % 10 betekent "de rest na delen door 10"
    return 0


def codeboek(paar):
    """Zet twee cijfers om in een teken: 01=A ... 26=Z, 00=spatie, 30-39 = cijfer 0-9."""
    getal = int(paar)
    if getal == 0:
        return " "
    if 1 <= getal <= 26:
        return chr(ord("A") + getal - 1)
    # TODO: maak de cijfers 30 t/m 39 af (30 = "0", 31 = "1", ...)
    return "?"


cijfers = ""
for groep, sleutelgroep in zip(bericht, sleutel):
    for c, s in zip(groep, sleutelgroep):
        cijfers += str(ontcijfer(int(c), int(s)))

print("Ontcijferde cijfers:", cijfers)

tekst = ""
for i in range(0, len(cijfers), 2):
    tekst += codeboek(cijfers[i:i + 2])
print("Het bericht luidt:  ", tekst)
