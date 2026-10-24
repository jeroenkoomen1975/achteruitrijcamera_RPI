# Koomen.IT – WordPress portfolio-thema

Portfolio-thema in de huisstijl van **koomen.it**: helder blauw, donker leisteen en het
netwerkmotief uit het logo. Gemaakt om programma's, websites en hardwareprojecten met
**ESP32, Arduino en Raspberry Pi** te laten zien.

![Screenshot](koomenit/screenshot.png)

## Installeren

1. Maak een zip van de map: `cd wordpress-theme && zip -r koomenit.zip koomenit`
2. Ga in WordPress naar **Weergave → Thema's → Nieuw thema toevoegen → Thema uploaden**.
3. Upload `koomenit.zip` en klik op **Activeren**.

Bij het activeren maakt het thema automatisch de platforms *ESP32*, *Arduino* en
*Raspberry Pi* aan, en de projecttypes *Website / webinterface*, *Sensorproject* en
*Programma / software*. Het projectoverzicht staat op `/projecten/`.

Zet bij **Instellingen → Algemeen** de sitetitel op `koomen.it`: het deel na de punt (`.it`)
krijgt dan automatisch de grijze kleur uit het logo.

## Een project toevoegen

Ga in het dashboard naar **Projecten → Nieuw project**.

| Veld                       | Waar het verschijnt |
|----------------------------|---------------------|
| Titel en inhoud            | Projectpagina (gebruik gerust tabellen, codeblokken en afbeeldingen) |
| Samenvatting               | Projectkaart en onder de titel op de projectpagina |
| Projectafbeelding          | Projectkaart en bovenaan de projectpagina; zonder afbeelding toont de kaart een platformicoon |
| Platform                   | Badges, filterknoppen en de platformkaarten op de homepage |
| Projecttype                | Label op de kaart en bij de projectgegevens |
| Technieken                 | Labels op de kaart (C++, Python, MQTT, BME280, …) |
| Projectgegevens (zijbalk)  | Status, jaar, GitHub-link, demo-link en de hardwarelijst |

## Homepage aanpassen

Alles staat in **Weergave → Customizer → Koomen.IT thema-opties**. Elke sectie kan aan of uit.

| Sectie              | Opties |
|---------------------|--------|
| Kleuren & stijl     | Blauw, lichtblauw, netwerkgrijs, donkere achtergrond, koppen, lichte achtergrond, tekst, afronding |
| Typografie          | Lettertype voor koppen en tekst |
| Header & topbalk    | Topbalk aan/uit, e-mail, telefoon, knop rechtsboven |
| Hero                | Titel, tekst, knoppen, illustratie (seriële monitor met eigen regels, logo of niets), achtergrondafbeelding |
| Platforms           | Tot 4 kaarten. Heet een kaart hetzelfde als een platform, dan linkt hij automatisch naar dat platform en toont hij het aantal projecten |
| Projecten           | Titel, intro, aantal projecten, filterknoppen aan/uit |
| Over mij            | Tekst, vaardigheden, foto, knop (standaard naar GitHub) |
| Cijfers             | Tot 4 cijfers, automatisch geteld (projecten, platforms, technieken) of zelf ingevuld |
| Blog                | Nieuwste berichten |
| Call-to-action      | Titel, tekst en knop |
| Footer & social     | Omschrijving, adres, GitHub, LinkedIn, YouTube, Instagram |

Verder: logo-favicon (als er geen site-icoon is ingesteld), twee menulocaties, zijbalk en drie
footer-widgetkolommen, het koomen.it-kleurenpalet in de blokeditor en een paginasjabloon
**Volledige breedte**.

## Demo

`demo/` bevat een statische weergave van de homepage, het projectoverzicht en een
projectpagina, gemaakt uit de echte thema-templates:

```bash
php wordpress-theme/demo/render-demo.php
```

Het project *Achteruitrij-parkeersensor* is echt; de andere projecten en blogberichten zijn
voorbeelden.
