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




// ----------------------------------------------------------------------



// Bewerbungsdoku - Details (nur lesen), gleiche Optik wie Karten



// ----------------------------------------------------------------------



require_once(__DIR__ . '/../../config.php');







$id      = required_param('id', PARAM_INT);       // course module id



$entryid = required_param('entryid', PARAM_INT);   // entry id







$cm = get_coursemodule_from_id('bewerbungsdoku', $id, 0, false, MUST_EXIST);



$instance = $DB->get_record('bewerbungsdoku', ['id'=>$cm->instance], '*', MUST_EXIST);



$course   = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);







require_login($course, true, $cm);



$context = context_module::instance($cm->id);



require_capability('mod/bewerbungsdoku:view', $context);



$canviewall = has_capability('mod/bewerbungsdoku:viewallentries', $context);







$entry = $DB->get_record_sql("SELECT e.*, u.firstname, u.lastname, u.email



                                FROM {bewerbungsdoku_entries} e



                                JOIN {user} u ON u.id = e.userid



                               WHERE e.id = :eid", ['eid'=>$entryid], MUST_EXIST);



if (!$canviewall && $entry->userid != $USER->id) {



    print_error('nopermissions', 'error', '', 'view this entry');



}







$PAGE->set_url('/mod/bewerbungsdoku/details.php', ['id'=>$cm->id,'entryid'=>$entryid]);



$PAGE->set_title(format_string($instance->name));



$PAGE->set_heading($course->fullname);







// CSS aus dem Modul



$PAGE->requires->css(new moodle_url('/mod/bewerbungsdoku/styles.css'));







echo $OUTPUT->header();



echo html_writer::start_div('', ['id'=>'page-mod-bewerbungsdoku-view']);







$allowedstatus = ['open','inprogress','done','unspecified','pendingreply','interview','shortlist','rejected','other'];



$statuskey = in_array($entry->status,$allowedstatus) ? $entry->status : 'other';







$name    = fullname((object)['firstname'=>$entry->firstname,'lastname'=>$entry->lastname]);



$email   = s($entry->email);



$created = userdate($entry->timecreated, '%d.%m.%Y, %H:%M');



$applied = userdate($entry->eventtime,   '%d.%m.%Y, %H:%M');



$org     = s($entry->organisation);



$role    = s($entry->title);







// Zusatzfelder (falls vorhanden)



$contactperson = property_exists($entry,'contactperson') ? s($entry->contactperson) : '';



$contactmail   = property_exists($entry,'contactemail')  ? s($entry->contactemail)  : '';



$contactphone  = property_exists($entry,'contactphone')  ? s($entry->contactphone)  : '';



$contactway    = property_exists($entry,'commmethod')    ? s($entry->commmethod)    : '';



$notes         = property_exists($entry,'notes')         ? trim($entry->notes)      : '';



$nextsteps     = property_exists($entry,'nextsteps')     ? trim($entry->nextsteps)  : '';







echo html_writer::start_div('bdd-card');







    echo html_writer::start_div('bdd-card__head');



        echo html_writer::tag('h3', s($name).' | '.html_writer::span($email,'bdd-mail'), ['class'=>'bdd-title']);



        echo html_writer::start_div('bdd-head__right');



            echo html_writer::tag('span', get_string('status_'.$statuskey,'mod_bewerbungsdoku'),



                ['class'=>'bd-status bd-status--'.$statuskey]);



        echo html_writer::end_div();



    echo html_writer::end_div();







    echo html_writer::start_div('bdd-meta');



        echo html_writer::tag('div', html_writer::span('Erstellt:','bdd-meta__label').' '.$created, ['class'=>'bdd-meta__item']);



        echo html_writer::tag('div', html_writer::span('Bewerbung:','bdd-meta__label').' '.$applied, ['class'=>'bdd-meta__item']);



        echo html_writer::tag('div', html_writer::span('Firma/Betrieb:','bdd-meta__label').' '.$org, ['class'=>'bdd-meta__item']);



        echo html_writer::tag('div', html_writer::span('Beschäftigung als:','bdd-meta__label').' '.$role, ['class'=>'bdd-meta__item']);



        if ($contactperson !== '') echo html_writer::tag('div', html_writer::span('Kontaktperson:','bdd-meta__label').' '.$contactperson, ['class'=>'bdd-meta__item']);



        if ($contactmail   !== '') echo html_writer::tag('div', html_writer::span('Kontakt E‑Mail:','bdd-meta__label').' '.$contactmail, ['class'=>'bdd-meta__item']);



        if ($contactphone  !== '') echo html_writer::tag('div', html_writer::span('Telefon:','bdd-meta__label').' '.$contactphone, ['class'=>'bdd-meta__item']);



        if ($contactway    !== '') echo html_writer::tag('div', html_writer::span('Kontaktweg:','bdd-meta__label').' '.$contactway, ['class'=>'bdd-meta__item']);



    echo html_writer::end_div();







    // Notizen



    echo html_writer::tag('h4','Notizen', ['class'=>'mt-3']);



    if ($notes !== '') {



        echo html_writer::div(format_text($notes, FORMAT_HTML));



    } else {



        echo html_writer::div('– keine –', 'text-muted');



    }







    // Nächste Schritte



    echo html_writer::tag('h4','Nächste Schritte', ['class'=>'mt-3']);



    if ($nextsteps !== '') {



        echo html_writer::div(format_text($nextsteps, FORMAT_HTML));



    } else {



        echo html_writer::div('– keine –', 'text-muted');



    }







    // Anhänge



    echo html_writer::tag('h4','Anhänge', ['class'=>'mt-3']);



    $fs = get_file_storage();



    $files = $fs->get_area_files($context->id, 'mod_bewerbungsdoku', 'attachment', $entry->id, 'filename', false);



    if ($files) {



        $list = html_writer::start_tag('ul');



        foreach ($files as $file) {



            $url = moodle_url::make_pluginfile_url($context->id, 'mod_bewerbungsdoku', 'attachment', $entry->id, '/', $file->get_filename());



            $list .= html_writer::tag('li', html_writer::link($url, s($file->get_filename())));



        }



        $list .= html_writer::end_tag('ul');



        echo $list;



    } else {



        echo html_writer::div('– keine –', 'text-muted');



    }







echo html_writer::end_div(); // card







echo $OUTPUT->single_button(new moodle_url('/mod/bewerbungsdoku/view.php', ['id'=>$cm->id]), get_string('back'), 'get');



echo html_writer::end_div(); // wrapper



echo $OUTPUT->footer();



