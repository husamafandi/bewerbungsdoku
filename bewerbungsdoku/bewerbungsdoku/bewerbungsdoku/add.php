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



$cm = get_coursemodule_from_id('bewerbungsdoku', $id, 0, false, MUST_EXIST);



$instance = $DB->get_record('bewerbungsdoku', ['id' => $cm->instance], '*', MUST_EXIST);



$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);







require_login($course, true, $cm);



$context = context_module::instance($cm->id);



require_capability('mod/bewerbungsdoku:submit', $context);







$PAGE->set_url('/mod/bewerbungsdoku/add.php', ['id' => $cm->id]);



$PAGE->set_title(format_string($instance->name));



$PAGE->set_heading($course->fullname);







$mform = new mod_bewerbungsdoku_entry_form($PAGE->url->out(false), ['context' => $context]);







$mform->set_data((object)['id' => $cm->id, 'cmid' => $cm->id]);







if ($mform->is_cancelled()) {



    redirect(new moodle_url('/mod/bewerbungsdoku/view.php', ['id' => $cm->id]));



}







if ($data = $mform->get_data()) {



    $entry = new stdClass();



    $entry->bewerbungsdokuid = $instance->id;



    $entry->userid = $USER->id;



    $entry->entrytype = $data->entrytype;



    $entry->title = $data->title;



    // removed: participantemail is derived from user profile



    $entry->participantemail = '';



    $entry->organisation = $data->organisation;



    $entry->contactname = $data->contactname;



    $entry->contactemail = $data->contactemail;



    $entry->contactphone = $data->contactphone;



    $entry->commmethod = $data->commmethod;



    $entry->eventtime = $data->eventtime;



    $entry->status = $data->status;



    $entry->notes = $data->notes['text'] ?? '';



    $entry->nextsteps = $data->nextsteps['text'] ?? '';



    $entry->timecreated = time();



    $entry->timemodified = $entry->timecreated;







    $entryid = $DB->insert_record('bewerbungsdoku_entries', $entry);







    // Save attachments.



    $draftid = file_get_submitted_draft_itemid('attachments');



    file_save_draft_area_files($draftid, $context->id, 'mod_bewerbungsdoku', 'attachment', $entryid, [



        'subdirs' => 0,



        'maxfiles' => -1,



        'maxbytes' => 0



    ]);







    redirect(new moodle_url('/mod/bewerbungsdoku/view.php', ['id' => $cm->id]));



}







echo $OUTPUT->header();



echo $OUTPUT->heading(get_string('addentry', 'mod_bewerbungsdoku'));



$mform->display();



echo $OUTPUT->footer();



