# ===================== POST 5: PROGRAMMEREN =====================
#
# De Nachtzender versleutelt de kluiscode met een "one-time pad":
#
#     versleuteld cijfer = (kluiscode-cijfer + sleutel-cijfer) mod 10
#
# Voorbeeld: kluiscode 3, sleutel 9  ->  (3 + 9) mod 10 = 12 mod 10 = 2
#
# Jullie hebben het versleutelde bericht (van de zender) en de 4
# sleutelcijfers (van de posten). Vul ze hieronder in, maak de functie
# af en druk op Run (F5).

bericht = [0, 0, 0, 0]   # de 4 cijfers die de zender omroept
sleutel = [0, 0, 0, 0]   # sleuteldeel 1, 2, 3 en 4


def ontcijfer(versleuteld, sleutel):
    # TODO: we moeten de versleuteling terugdraaien.
    # Tip: het tegenovergestelde van optellen is ...
    # Tip: % 10 betekent "mod 10" (de rest na delen door 10)
    return 0


kluiscode = []
for i in range(4):
    kluiscode.append(ontcijfer(bericht[i], sleutel[i]))

print("De kluiscode is:", kluiscode)
