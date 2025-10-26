# Bewerbungsdokumentation (mod_bewerbungsdoku)

Aktivitäts‑Plugin für Moodle 4.1+, mit dem Teilnehmer_innen im Kurs Bewerbungsdokumentationen anlegen (Termine, Kontakte, Bewerbungen, Notizen, Anhänge). Lehrende sehen Reports und können CSV exportieren.

## Features
- Eigenständige Aktivität pro Kurs (pro Kurs beliebig viele Instanzen möglich)
- Einträge pro Teilnehmer_in (nur eigene sichtbar), Lehrende können alle sehen
- Felder: Typ, Titel, Organisation, Kontakt, Datum/Uhrzeit, Kontaktweg, Status, Notizen, Nächste Schritte, Anhänge
- CSV‑Export (alle/je Nutzer_in)
- DSGVO/Privacy‑Provider enthalten

## Installation
1. ZIP in `moodle/mod/` entpacken (es entsteht der Ordner `moodle/mod/bewerbungsdoku`).
2. Als Administrator:in **Website-Administration → Mitteilungen** öffnen und Installation abschließen.
3. In einem Kurs **Aktivität oder Material anlegen → Bewerbungsdokumentation**.

## Rollen & Fähigkeiten
- `mod/bewerbungsdoku:addinstance`
- `mod/bewerbungsdoku:view`
- `mod/bewerbungsdoku:submit`
- `mod/bewerbungsdoku:viewallentries`
- `mod/bewerbungsdoku:exportentries`
