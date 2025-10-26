<?php
/**
 * This file is part of the bewerbungsdoku_unpacked plugin for Moodle.
 *
 * Copyright (C) 2025 Husam Afandi
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @package   bewerbungsdoku_unpacked
 * @author    Husam Afandi
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */




// German language strings for mod_bewerbungsdoku.







$string['pluginname'] = 'Bewerbungsdokumentation';



$string['modulename'] = 'Bewerbungsdokumentation';



$string['modulenameplural'] = 'Bewerbungsdokumentationen';



$string['modulename_help'] = '<p><strong>Bewerbungsdokumentation</strong> unterstützt die durchgängige Erfassung von Bewerbungen, Kontakten und Rückmeldungen – übersichtlich für Teilnehmer_innen, auswertbar für Trainer_innen.</p>



<p><strong>Teilnehmer_innen‑Sicht:</strong> Eigene Einträge anlegen, bearbeiten und löschen; Anhänge hochladen (z.&nbsp;B. Lebenslauf, E‑Mail‑PDF); den <em>Status der Bewerbung</em> wählen und aktualisieren; das Feld <em>Datum der Bewerbung</em> setzen; sowie Notizen zum Verlauf hinzufügen. Die Tabelle zeigt alle eigenen Datensätze mit Spalten für Firma/Betrieb, Tätigkeit, Status und Datum.</p>



<p>Filter stehen direkt über der Liste: Nach <em>Status</em> filtern (z.&nbsp;B. „Absage“, „Vorstellungstermin“), nach Alphabet (A–Z) sowie nach Zeitraum „Erstellt am“. Einträge lassen sich in wenigen Klicks finden.</p>



<p><strong>Trainer_innen‑Sicht:</strong> Alle Einträge der Gruppe oder des Kurses sind sichtbar. Trainer_innen können nach Status, Name und Datum filtern, die gefilterte Ansicht als <em>CSV</em> exportieren und bei Bedarf die Datenlage der Teilnehmenden prüfen.</p>



<p><strong>Export:</strong> Der CSV‑Export steht Trainer_innen zur Verfügung und übernimmt die aktiven Filter. Für Teilnehmer_innen kann ein PDF‑Export ihrer eigenen Einträge bereitgestellt werden.</p>



<p><strong>Kompatibilität & Datenschutz:</strong> Die Aktivität ist für Moodle&nbsp;4.1 ausgelegt. Personenbezogene Daten bleiben innerhalb des Kurses; optional können Datenschutzhinweise (GDPR) ergänzt werden.</p>



<p><span style="color:#6b7280"><strong>Entwickler:</strong> Husam Afandi</span></p>';







$string['pluginadministration'] = 'Administration Bewerbungsdokumentation';







$string['name'] = 'Name';



$string['intro'] = 'Beschreibung';







$string['addentry'] = 'Eintrag hinzufügen';



$string['editentry'] = 'Eintrag bearbeiten';



$string['deleteentry'] = 'Eintrag löschen';



$string['confirmdelete'] = 'Diesen Eintrag wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.';







$string['entrytype'] = 'Typ';



$string['entrytype_help'] = 'Art des Eintrags (Termin, Kontakt, Bewerbung, Sonstiges).';



$string['title'] = 'Beschäftigung als';



$string['organisation'] = 'Firma/Betrieb';



$string['contactname'] = 'Kontaktperson';



$string['contactemail'] = 'Woher kam die Stelleninfo';



$string['contactphone'] = 'Telefon';



$string['commmethod'] = 'Wie erfolgte die Bewerbung';



$string['eventtime'] = 'Datum (wann haben Sie sich beworben)';



$string['status'] = 'Status der Bewerbung';



$string['notes'] = 'Notizen';



$string['nextsteps'] = 'Nächste Schritte';



$string['attachments'] = 'Anhänge';







$string['type_appointment'] = 'Termin';



$string['type_contact'] = 'Kontakt';



$string['type_application'] = 'Bewerbung';



$string['type_other'] = 'Sonstiges';







$string['status_open'] = 'offen';



$string['status_inprogress'] = 'in Bearbeitung';



$string['status_done'] = 'abgeschlossen';







$string['comm_email'] = 'E‑Mail';



$string['comm_phone'] = 'Telefon';



$string['comm_inperson'] = 'Persönlich';



$string['comm_online'] = 'Online';



$string['comm_unspecified'] = '-- Bitte auswählen --';







$string['noentries'] = 'Noch keine Einträge vorhanden.';



$string['myentries'] = 'Meine Einträge';



$string['allentries'] = 'Alle Einträge';







$string['exportcsv'] = 'CSV exportieren';



$string['exportown'] = 'Eigene Einträge exportieren';







$string['privacy:metadata:bewerbungsdoku_entries'] = 'Bewerbungsdokumentations‑Einträge';



$string['privacy:metadata:bewerbungsdoku_entries:userid'] = 'Nutzer_in, die den Eintrag erstellt hat';



$string['privacy:metadata:bewerbungsdoku_entries:fields'] = 'Felder: Typ, Titel, Organisation, Kontakte, Datum, Status, Notizen, Nächste Schritte';







$string['evententrycreated'] = 'Eintrag erstellt';



$string['evententryupdated'] = 'Eintrag aktualisiert';



$string['evententrydeleted'] = 'Eintrag gelöscht';







$string['bewerbungsdoku:addinstance'] = 'Aktivität „Bewerbungsdokumentation“ zum Kurs hinzufügen';







$string['bewerbungsdoku:view'] = 'Bewerbungsdokumentation ansehen';







$string['bewerbungsdoku:submit'] = 'Eigene Einträge anlegen und bearbeiten';







$string['bewerbungsdoku:viewallentries'] = 'Alle Einträge sehen';







$string['bewerbungsdoku:exportentries'] = 'Einträge exportieren';







$string['csv_userid'] = 'Nutzer-ID';







$string['csv_type'] = 'Typ';







$string['csv_title'] = 'Titel';







$string['csv_organisation'] = 'Name der Firma';







$string['csv_contactname'] = 'Kontaktperson';







$string['csv_contactemail'] = 'E‑Mail';







$string['csv_contactphone'] = 'Telefon';







$string['csv_commmethod'] = 'Kontaktweg';







$string['csv_eventtime'] = 'Datum & Uhrzeit';







$string['csv_status'] = 'Status';







$string['csv_notes'] = 'Notizen';







$string['csv_nextsteps'] = 'Nächste Schritte';







$string['csv_timecreated'] = 'Erstellt am';







$string['csv_timemodified'] = 'Geändert am';







$string['participantemail'] = 'Mein E-Mail Adresse';



$string['participantemail_help'] = 'Eigene E-Mail-Adresse der/des Teilnehmer_in.';







$string['participantemail_col'] = 'Mein E‑Mail';







$string['participants_col'] = 'Teilnehmer_innen';







$string['participantemail_col_admin'] = 'E‑Mail Teilnehmer_in';











$string['csv_userfullname'] = 'Teilnehmer/in';



$string['csv_useremail'] = 'E‑Mail Teilnehmer/in';







$string['createdat'] = 'Erstellt am';



$string['csv_createdat'] = 'Datum';



$string['eventtime_application'] = 'Wann beworben?';



$string['eventtime_appointment'] = 'Wann ist der Termin?';



$string['eventtime_contact'] = 'Wann Kontakt?';



$string['eventtime_other'] = 'Wann?';







$string['exportpdf'] = 'PDF exportieren';







$string['comm_letter'] = 'schriftlich';



$string['comm_other'] = 'Sonstiges';



$string['status_unspecified'] = '-- Bitte auswählen --';



$string['status_pendingreply'] = 'Antwort des Betriebes offen';



$string['status_interview'] = 'Vorstelltermin';



$string['status_shortlist'] = 'in Vormerkung';



$string['status_rejected'] = 'Absage';



$string['status_other'] = 'Sonstiges';







$string['status_pending'] = 'Antwort des Betriebes offen';







$string['allgroups_local'] = 'Alle Gruppen';







$string['appliedat'] = 'Datum der Bewerbung';







$string['csv_appliedat'] = 'Datum der Bewerbung';



