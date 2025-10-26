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




// English language strings for mod_bewerbungsdoku.







$string['pluginname'] = 'Application Log';



$string['modulename'] = 'Application Log';



$string['modulenameplural'] = 'Application Logs';



$string['modulename_help'] = '<p><strong>Application log</strong> helps learners record applications, contacts and outcomes – clear for participants, and easy to evaluate for teachers.</p>



<p><strong>Participant view:</strong> Create, edit and delete own entries; upload attachments (e.g. CV, PDF emails); choose and update the <em>application status</em>; set the <em>application date</em>; and add notes. The table lists all personal records with columns for company, role, status and date.</p>



<p>Filters above the list allow searching by <em>status</em> (e.g. “Declined”, “Interview”), alphabet (A–Z) and “Created on” date range.</p>



<p><strong>Teacher view:</strong> Teachers see all entries in the course or group. They can filter by status, name and date, export the filtered view to <em>CSV</em>, and review learner progress.</p>



<p><strong>Data model:</strong> Each record stores type/title, company, role, communication, status, application date, notes and optional attachments.</p>



<p><strong>Editing rules:</strong> Participants may edit <em>only their own</em> entries; teachers manage the overview. Permissions rely on Moodle capabilities.</p>



<p><strong>Display:</strong> Dates are shown in a clear localized format and status options are consolidated.</p>



<p><strong>Export:</strong> CSV export is available for teachers and respects active filters. A personal PDF export can be provided to participants.</p>



<p><strong>Compatibility & privacy:</strong> Designed for Moodle&nbsp;4.1. Personal data stays within the course; GDPR notes can be added if required.</p>



<p><span style="color:#6b7280"><strong>Developer:</strong> Husam Afandi</span></p>';







$string['pluginadministration'] = 'Application Log administration';







$string['name'] = 'Name';



$string['intro'] = 'Description';







$string['addentry'] = 'Add entry';



$string['editentry'] = 'Edit entry';



$string['deleteentry'] = 'Delete entry';



$string['confirmdelete'] = 'Really delete this entry? This action cannot be undone.';







$string['entrytype'] = 'Type';



$string['title'] = 'Participant full name';



$string['organisation'] = 'Company name';



$string['contactname'] = 'Contact person';



$string['contactemail'] = 'Company email';



$string['contactphone'] = 'Phone';



$string['commmethod'] = 'How did you apply';



$string['eventtime'] = 'Date (when you applied)';



$string['status'] = 'Application status';



$string['notes'] = 'Notes';



$string['nextsteps'] = 'Next steps';



$string['attachments'] = 'Attachments';







$string['type_appointment'] = 'Appointment';



$string['type_contact'] = 'Contact';



$string['type_application'] = 'Application';



$string['type_other'] = 'Other';







$string['status_open'] = 'open';



$string['status_inprogress'] = 'in progress';



$string['status_done'] = 'done';







$string['comm_email'] = 'E‑mail';



$string['comm_phone'] = 'Phone';



$string['comm_inperson'] = 'In person';



$string['comm_online'] = 'Online';



$string['comm_unspecified'] = '-- Please select --';







$string['noentries'] = 'No entries yet.';



$string['myentries'] = 'My entries';



$string['allentries'] = 'All entries';







$string['exportcsv'] = 'Export CSV';



$string['exportown'] = 'Export own entries';







$string['privacy:metadata:bewerbungsdoku_entries'] = 'Application log entries';



$string['privacy:metadata:bewerbungsdoku_entries:userid'] = 'The user who created the entry';



$string['privacy:metadata:bewerbungsdoku_entries:fields'] = 'Fields: type, title, organisation, contacts, date, status, notes, next steps';







$string['evententrycreated'] = 'Entry created';



$string['evententryupdated'] = 'Entry updated';



$string['evententrydeleted'] = 'Entry deleted';







$string['bewerbungsdoku:addinstance'] = 'Add an Application Log activity to the course';







$string['bewerbungsdoku:view'] = 'View Application Log';







$string['bewerbungsdoku:submit'] = 'Create and edit own entries';







$string['bewerbungsdoku:viewallentries'] = 'View all entries';







$string['bewerbungsdoku:exportentries'] = 'Export entries';







$string['csv_userid'] = 'User ID';







$string['csv_type'] = 'Type';







$string['csv_title'] = 'Title';







$string['csv_organisation'] = 'Company name';







$string['csv_contactname'] = 'Contact name';







$string['csv_contactemail'] = 'Contact email';







$string['csv_contactphone'] = 'Contact phone';







$string['csv_commmethod'] = 'Communication';







$string['csv_eventtime'] = 'Date & time';







$string['csv_status'] = 'Status';







$string['csv_notes'] = 'Notes';







$string['csv_nextsteps'] = 'Next steps';







$string['csv_timecreated'] = 'Time created';







$string['csv_timemodified'] = 'Time modified';







$string['participantemail'] = 'Participant email';



$string['participantemail_help'] = "Learner's own email address.";







$string['participantemail_col'] = 'My email';







$string['participants_col'] = 'Participants';







$string['participantemail_col_admin'] = 'Participant email';











$string['csv_userfullname'] = 'User';



$string['csv_useremail'] = 'User e-mail';







$string['createdat'] = 'Created at';



$string['csv_createdat'] = 'Created at';



$string['eventtime_application'] = 'When applied?';



$string['eventtime_appointment'] = 'Appointment time?';



$string['eventtime_contact'] = 'When contacted?';



$string['eventtime_other'] = 'When?';







$string['exportpdf'] = 'Export PDF';







$string['comm_letter'] = 'letter';



$string['comm_other'] = 'other';



$string['status_unspecified'] = '-- Please select --';



$string['status_pendingreply'] = 'Employer reply pending';



$string['status_interview'] = 'Interview scheduled';



$string['status_shortlist'] = 'Shortlisted';



$string['status_rejected'] = 'Rejected';



$string['status_other'] = 'Other';







$string['status_pending'] = 'Employer reply pending';







$string['allgroups_local'] = 'All groups';







$string['appliedat'] = 'Application date';







$string['csv_appliedat'] = 'Application date';



