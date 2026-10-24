# Koomen.IT – WordPress-thema

Modern WordPress-thema in de huisstijl van **Koomen.IT – Training, Onderwijs & Advies**:
navy en teal uit het logo, het lettertype Inter, de `</>`-code-tag als labelmotief en het
netwerk van knooppunten uit het logo als achtergrond van de hero.
Vrijwel alles is aan te passen via **Weergave → Customizer → Koomen.IT thema-opties**.

![Screenshot](koomenit/screenshot.png)

Zet bij **Instellingen → Algemeen** de sitetitel op `koomen.it` en de ondertitel op
`Training, Onderwijs & Advies`; zonder eigen logo toont de header dan het K-logo met die tekst,
precies zoals het logo met naam.

## Installeren

1. Pak de map `koomenit/` in als zip: `cd wordpress-theme && zip -r koomenit.zip koomenit`
2. Ga in WordPress naar **Weergave → Thema's → Nieuwe toevoegen → Thema uploaden**.
3. Upload `koomenit.zip` en activeer het thema.
4. Maak onder **Weergave → Menu's** een menu aan en koppel het aan *Hoofdmenu* (en eventueel *Footermenu*).

## Wat kun je aanpassen?

| Customizer-sectie   | Opties |
|---------------------|--------|
| Kleuren & stijl     | Navy, teal-accent, staalblauw, netwerkgrijs, donkere footerkleur, lichte achtergrond, tekstkleur, afronding van hoeken |
| Typografie          | Lettertype voor koppen en tekst (Google Fonts) |
| Header & topbalk    | Topbalk aan/uit, tekst, telefoon, e-mail, knop rechtsboven |
| Hero                | Label, titel, tekst, twee knoppen, logo-illustratie aan/uit, achtergrondafbeelding, sterkte van de overlay |
| Aanbod              | Titel, intro en tot 4 onderdelen (icoon, titel, tekst, link). Standaard: Training, Onderwijs en Advies. Een lege titel verbergt het onderdeel |
| Over                | Titel, tekst, voordelen (één per regel), afbeelding, knop |
| Cijfers             | Tot 4 kerncijfers met optelanimatie (vul je eigen cijfers in; een leeg label verbergt het cijfer) |
| Nieuws              | Titel en aantal berichten |
| Call-to-action      | Titel, tekst en knop |
| Footer & social     | Omschrijving, adres, copyright, LinkedIn/Facebook/Instagram/X |

Elke homepage-sectie kan apart aan- of uitgezet worden. Daarnaast:

- Eigen logo (**Site-identiteit**), twee menulocaties, zijbalk en drie footer-widgetkolommen.
- Ondersteuning voor de blokeditor met het Koomen.IT-kleurenpalet en verlopen (`theme.json`).
- Paginasjabloon **Volledige breedte** om met blokken eigen landingspagina's te bouwen.
- Responsief met mobiel menu, sticky header en subtiele scroll-animaties
  (uitgeschakeld bij `prefers-reduced-motion`).

## Demo

`demo/index.html` is een statische weergave van de homepage. Hij wordt gemaakt uit de echte
thema-templates met:

```bash
php wordpress-theme/demo/render-demo.php > wordpress-theme/demo/index.html
```
