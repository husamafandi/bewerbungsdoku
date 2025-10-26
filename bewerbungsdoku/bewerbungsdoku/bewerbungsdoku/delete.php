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







$id = required_param('id', PARAM_INT); // cmid



$entryid = required_param('entryid', PARAM_INT);



require_sesskey();







$cm = get_coursemodule_from_id('bewerbungsdoku', $id, 0, false, MUST_EXIST);



$instance = $DB->get_record('bewerbungsdoku', ['id' => $cm->instance], '*', MUST_EXIST);



$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);



$entry = $DB->get_record('bewerbungsdoku_entries', ['id' => $entryid], '*', MUST_EXIST);







require_login($course, true, $cm);



$context = context_module::instance($cm->id);



$canviewall = has_capability('mod/bewerbungsdoku:viewallentries', $context);



if (!$canviewall && $entry->userid != $USER->id) {



    print_error('nopermissions');



}







if (optional_param('confirm', 0, PARAM_BOOL)) {



    bewerbungsdoku_delete_entry_files($entryid);



    $DB->delete_records('bewerbungsdoku_entries', ['id' => $entryid]);



    redirect(new moodle_url('/mod/bewerbungsdoku/view.php', ['id' => $cm->id]));



}







$PAGE->set_url('/mod/bewerbungsdoku/delete.php', ['id' => $cm->id, 'entryid' => $entryid]);



$PAGE->set_title(format_string($instance->name));



$PAGE->set_heading($course->fullname);







echo $OUTPUT->header();



echo $OUTPUT->confirm(get_string('confirmdelete', 'mod_bewerbungsdoku'),



    new moodle_url('/mod/bewerbungsdoku/delete.php', ['id' => $cm->id, 'entryid' => $entryid, 'confirm' => 1, 'sesskey' => sesskey()]),



    new moodle_url('/mod/bewerbungsdoku/view.php', ['id' => $cm->id])



);



echo $OUTPUT->footer();



