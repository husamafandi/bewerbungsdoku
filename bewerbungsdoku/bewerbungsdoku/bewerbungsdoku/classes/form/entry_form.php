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




defined('MOODLE_INTERNAL') || die();



require_once($CFG->libdir . '/formslib.php');







class mod_bewerbungsdoku_entry_form extends moodleform {



    public function definition() {



        $mform   = $this->_form;



        $context = $this->_customdata['context'];







        // --- Hidden defaults (keep backend stable) ---



        $mform->addElement('hidden', 'entrytype', 'application');



        $mform->setType('entrytype', PARAM_ALPHANUMEXT);







        // --- Visible fields in requested order ---







        // 1) Datum *



        $mform->addElement('date_time_selector', 'eventtime', get_string('eventtime', 'mod_bewerbungsdoku'), ['optional' => false]);



        $mform->addRule('eventtime', null, 'required', null, 'client');







        // 2) Firma/Betrieb *



        $mform->addElement('text', 'organisation', get_string('organisation', 'mod_bewerbungsdoku'));



        $mform->setType('organisation', PARAM_TEXT);



        $mform->addRule('organisation', null, 'required', null, 'client');







        // 3) Kontaktperson



        $mform->addElement('text', 'contactname', get_string('contactname', 'mod_bewerbungsdoku'));



        $mform->setType('contactname', PARAM_TEXT);







        // 4) Beschäftigung als *



        $mform->addElement('text', 'title', get_string('title', 'mod_bewerbungsdoku'));



        $mform->setType('title', PARAM_TEXT);



        $mform->addRule('title', null, 'required', null, 'client');







        // 5) Woher kam die Stelleninfo (freier Text)



        $mform->addElement('text', 'contactemail', get_string('contactemail', 'mod_bewerbungsdoku'));



        $mform->setType('contactemail', PARAM_TEXT);







        // 6) Wie erfolgte die Bewerbung * (Dropdown)



        $comms = [



            'unspecified' => get_string('comm_unspecified', 'mod_bewerbungsdoku'),



            'email'       => get_string('comm_email', 'mod_bewerbungsdoku'),



            'online'      => get_string('comm_online', 'mod_bewerbungsdoku'),



            'letter'      => get_string('comm_letter', 'mod_bewerbungsdoku'),



            'phone'       => get_string('comm_phone', 'mod_bewerbungsdoku'),



            'inperson'    => get_string('comm_inperson', 'mod_bewerbungsdoku'),



            'other'       => get_string('comm_other', 'mod_bewerbungsdoku'),



        ];



        $mform->addElement('select', 'commmethod', get_string('commmethod', 'mod_bewerbungsdoku'), $comms);



        $mform->addRule('commmethod', null, 'required', null, 'client');







        // 7) Status der Bewerbung * (Dropdown)



        $statuses = [



            'unspecified'    => get_string('status_unspecified', 'mod_bewerbungsdoku'),



            'pendingreply'   => get_string('status_pendingreply', 'mod_bewerbungsdoku'),



            'interview'      => get_string('status_interview', 'mod_bewerbungsdoku'),



            'shortlist'      => get_string('status_shortlist', 'mod_bewerbungsdoku'),



            'rejected'       => get_string('status_rejected', 'mod_bewerbungsdoku'),



            'other'          => get_string('status_other', 'mod_bewerbungsdoku'),



        ];



        $mform->addElement('select', 'status', get_string('status', 'mod_bewerbungsdoku'), $statuses);



        $mform->addRule('status', null, 'required', null, 'client');







        // --- Extras you asked to bring back ---



        $mform->addElement('editor', 'notes', get_string('notes', 'mod_bewerbungsdoku'));



        $mform->setType('notes', PARAM_RAW);







        $mform->addElement('editor', 'nextsteps', get_string('nextsteps', 'mod_bewerbungsdoku'));



        $mform->setType('nextsteps', PARAM_RAW);







        $fileoptions = ['subdirs' => 0, 'maxfiles' => -1, 'maxbytes' => 0, 'accepted_types' => '*'];



        $mform->addElement('filemanager', 'attachments', get_string('attachments', 'mod_bewerbungsdoku'), null, $fileoptions);







        // Hidden identifiers.



        $mform->addElement('hidden', 'id'); // cmid



        $mform->setType('id', PARAM_INT);



        $mform->addElement('hidden', 'entryid');



        $mform->setType('entryid', PARAM_INT);







        $this->add_action_buttons(true, get_string('savechanges'));



    }



}



