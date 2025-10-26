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







$courseid = required_param('id', PARAM_INT);



$course = get_course($courseid);



require_login($course);







$PAGE->set_url('/mod/bewerbungsdoku/index.php', ['id' => $course->id]);



$PAGE->set_title(get_string('modulenameplural', 'mod_bewerbungsdoku'));



$PAGE->set_heading($course->fullname);







echo $OUTPUT->header();



echo $OUTPUT->heading(get_string('modulenameplural', 'mod_bewerbungsdoku'));







if (!$cms = get_coursemodules_in_course('bewerbungsdoku', $course->id)) {



    echo $OUTPUT->notification(get_string('noentries', 'mod_bewerbungsdoku'));



    echo $OUTPUT->footer();



    exit;



}







echo html_writer::start_tag('ul');



foreach ($cms as $cm) {



    $name = format_string($cm->name, true);



    $link = new moodle_url('/mod/bewerbungsdoku/view.php', ['id' => $cm->id]);



    echo html_writer::tag('li', html_writer::link($link, $name));



}



echo html_writer::end_tag('ul');







echo $OUTPUT->footer();



