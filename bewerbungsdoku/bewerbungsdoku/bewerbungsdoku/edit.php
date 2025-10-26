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




require_once(__DIR__ . '/../../config.php');



require_once(__DIR__ . '/classes/form/entry_form.php');







$id = required_param('id', PARAM_INT); // cmid



$entryid = required_param('entryid', PARAM_INT);







$cm = get_coursemodule_from_id('bewerbungsdoku', $id, 0, false, MUST_EXIST);



$instance = $DB->get_record('bewerbungsdoku', ['id' => $cm->instance], '*', MUST_EXIST);



$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);



$entry = $DB->get_record('bewerbungsdoku_entries', ['id' => $entryid], '*', MUST_EXIST);







require_login($course, true, $cm);



$context = context_module::instance($cm->id);



$canviewall = has_capability('mod/bewerbungsdoku:viewallentries', $context);



if (!$canviewall && $entry->userid != $USER->id) {



    // Only owners or users with 'viewallentries' can edit.



    require_capability('mod/bewerbungsdoku:viewallentries', $context);



}







$canviewall = has_capability('mod/bewerbungsdoku:viewallentries', $context);



if (!$canviewall && $entry->userid != $USER->id) {



    print_error('nopermissions');



}







$PAGE->set_url('/mod/bewerbungsdoku/edit.php', ['id' => $cm->id, 'entryid' => $entryid]);



$PAGE->set_title(format_string($instance->name));



$PAGE->set_heading($course->fullname);







// Prepare form.



$entry->notes = ['text' => $entry->notes, 'format' => FORMAT_HTML];



$entry->nextsteps = ['text' => $entry->nextsteps, 'format' => FORMAT_HTML];







$mform = new mod_bewerbungsdoku_entry_form($PAGE->url->out(false), ['context' => $context]);



$formdata = clone $entry;



$formdata->id = $cm->id; // form hidden id = cmid



$formdata->cmid = $cm->id;



$formdata->entryid = $entryid;







$draftid = file_get_submitted_draft_itemid('attachments');



file_prepare_draft_area($draftid, $context->id, 'mod_bewerbungsdoku', 'attachment', $entryid, ['subdirs' => 0]);



$formdata->attachments = $draftid;







$mform->set_data($formdata);



if ($mform->is_cancelled()) {



    redirect(new moodle_url('/mod/bewerbungsdoku/view.php', ['id' => $cm->id]));



}







if ($data = $mform->get_data()) {



    $entry->entrytype = $data->entrytype;



    $entry->title = $data->title;



    $entry->participantemail = $data->participantemail ?? '';



    $entry->organisation = $data->organisation;



    $entry->contactname = $data->contactname;



    $entry->contactemail = $data->contactemail;



    $entry->contactphone = $data->contactphone;



    $entry->commmethod = $data->commmethod;



    $entry->eventtime = $data->eventtime;



    $entry->status = $data->status;



    $entry->notes = $data->notes['text'] ?? '';



    $entry->nextsteps = $data->nextsteps['text'] ?? '';



    $entry->timemodified = time();



    $entry->id = $entryid;







    $DB->update_record('bewerbungsdoku_entries', $entry);







    $draftid = file_get_submitted_draft_itemid('attachments');



    file_save_draft_area_files($draftid, $context->id, 'mod_bewerbungsdoku', 'attachment', $entryid, [



        'subdirs' => 0, 'maxfiles' => -1, 'maxbytes' => 0



    ]);







    redirect(new moodle_url('/mod/bewerbungsdoku/view.php', ['id' => $cm->id]));



}







echo $OUTPUT->header();



echo $OUTPUT->heading(get_string('editentry', 'mod_bewerbungsdoku'));



$mform->display();



echo $OUTPUT->footer();



