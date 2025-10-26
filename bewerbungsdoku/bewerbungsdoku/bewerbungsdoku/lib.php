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







require_once(__DIR__ . '/locallib.php'); // Optional future use.







function bewerbungsdoku_supports($feature) {



    switch ($feature) {



        case FEATURE_MOD_INTRO: return true;



        case FEATURE_SHOW_DESCRIPTION: return true;



        case FEATURE_BACKUP_MOODLE2: return false; // Basic DB tables are backup‑ready.



        default: return null;



    }



}







function bewerbungsdoku_add_instance($data, $mform = null) {



    global $DB, $USER;



    $data->timecreated = time();



    $data->timemodified = $data->timecreated;



    $id = $DB->insert_record('bewerbungsdoku', $data);



    return $id;



}







function bewerbungsdoku_update_instance($data, $mform = null) {



    global $DB;



    $data->id = $data->instance;



    $data->timemodified = time();



    return $DB->update_record('bewerbungsdoku', $data);



}







function bewerbungsdoku_delete_instance($id) {



    global $DB;



    if (!$instance = $DB->get_record('bewerbungsdoku', ['id' => $id])) {



        return false;



    }



    // Delete entries and files.



    $entries = $DB->get_records('bewerbungsdoku_entries', ['bewerbungsdokuid' => $instance->id]);



    foreach ($entries as $entry) {



        bewerbungsdoku_delete_entry_files($entry->id);



    }



    $DB->delete_records('bewerbungsdoku_entries', ['bewerbungsdokuid' => $instance->id]);



    $DB->delete_records('bewerbungsdoku', ['id' => $instance->id]);



    return true;



}







function bewerbungsdoku_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {



    require_login($course, true, $cm);







    if ($context->contextlevel != CONTEXT_MODULE) {



        return false;



    }







    if ($filearea !== 'attachment') {



        return false;



    }







    $entryid = (int)array_shift($args); // itemid = entry id







    global $DB, $USER;



    $entry = $DB->get_record('bewerbungsdoku_entries', ['id' => $entryid], '*', MUST_EXIST);







    $canviewall = has_capability('mod/bewerbungsdoku:viewallentries', $context);



    if (!$canviewall && $entry->userid != $USER->id) {



        return false;



    }







    $fs = get_file_storage();



    $relativepath = implode('/', $args);



    $fullpath = "/{$context->id}/mod_bewerbungsdoku/{$filearea}/{$entryid}/{$relativepath}";



    $file = $fs->get_file_by_hash(sha1($fullpath));







    if (!$file || $file->is_directory()) {



        return false;



    }







    send_stored_file($file, 0, 0, $forcedownload, $options);



}







function bewerbungsdoku_delete_entry_files(int $entryid): void {



    global $DB;



    // Fetch cm context via the entry's instance.



    $sql = "SELECT cm.id



              FROM {course_modules} cm



              JOIN {modules} m ON m.id = cm.module AND m.name = :modname



              JOIN {bewerbungsdoku} b ON b.id = cm.instance



              JOIN {bewerbungsdoku_entries} e ON e.bewerbungsdokuid = b.id



             WHERE e.id = :entryid";



    if ($rec = $DB->get_record_sql($sql, ['modname' => 'bewerbungsdoku', 'entryid' => $entryid])) {



        $context = context_module::instance($rec->id);



        $fs = get_file_storage();



        $fs->delete_area_files($context->id, 'mod_bewerbungsdoku', 'attachment', $entryid);



    }



}



