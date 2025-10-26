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



require_once($CFG->libdir . '/csvlib.class.php');







$id           = required_param('id', PARAM_INT);        // cmid



$mode         = optional_param('mode', 'all', PARAM_ALPHA);



$tifirst      = optional_param('tifirst', '', PARAM_RAW);



$tilast       = optional_param('tilast',  '', PARAM_RAW);



$sifirst      = optional_param('sifirst', '', PARAM_RAW);



$silast       = optional_param('silast',  '', PARAM_RAW);



$useridfilter = optional_param('userid', 0, PARAM_INT);



$datefrom     = optional_param('datefrom', '', PARAM_RAW);



$dateto       = optional_param('dateto',   '', PARAM_RAW);



$filter_status = optional_param('status','',PARAM_ALPHA);



$filter_comm   = optional_param('commmethod','',PARAM_ALPHA);



$filter_type   = optional_param('entrytype','',PARAM_ALPHA);



$filter_org    = optional_param('organisation','',PARAM_RAW_TRIMMED);



$filter_group  = optional_param('groupid', 0, PARAM_INT);







// CM / context.



$cm       = get_coursemodule_from_id('bewerbungsdoku', $id, 0, false, MUST_EXIST);



$instance = $DB->get_record('bewerbungsdoku', ['id' => $cm->instance], '*', MUST_EXIST);



$course   = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);







require_login($course, true, $cm);



$context    = context_module::instance($cm->id);



require_capability('mod/bewerbungsdoku:exportentries', $context);



$canviewall = has_capability('mod/bewerbungsdoku:viewallentries', $context);







// Build WHERE / JOIN.



$params   = ['bid' => $instance->id];



$where    = 'e.bewerbungsdokuid = :bid';



$join     = 'JOIN {user} u ON u.id = e.userid';



if ($filter_group > 0) {



    $join .= ' JOIN {groups_members} gm ON gm.userid = e.userid JOIN {groups} g ON g.id = gm.groupid';



    $where .= ' AND g.courseid = :gcourseid AND gm.groupid = :gid';



    $params['gcourseid'] = $course->id; $params['gid'] = $filter_group;



}







// Date range filter uses created time (more stable for entries).



if (!empty($dateon)) {



    $fromts = strtotime($dateon.' 00:00:00');



    $tots   = strtotime($dateon.' 23:59:59');



    if ($fromts) { $where .= ' AND e.timecreated >= :fromts'; $params['fromts'] = $fromts; }



    if ($tots)   { $where .= ' AND e.timecreated <= :tots';   $params['tots'] = $tots; }



}



if (!empty($datefrom)) {



    $fromts = strtotime($datefrom.' 00:00:00');



    if ($fromts) { $where .= ' AND e.timecreated >= :fromts'; $params['fromts'] = $fromts; }



}



if (!empty($dateto)) {



    $tots = strtotime($dateto.' 23:59:59');



    if ($tots) { $where .= ' AND e.timecreated <= :tots'; $params['tots'] = $tots; }



}







// Optional initials (Vor-/Nachname) only when exporting all entries.



if ($canviewall && $mode === 'all') {



    if ($tifirst !== '' || $sifirst !== '') {



        $needle = ($tifirst !== '') ? $tifirst : $sifirst;



        $where .= ' AND ' . $DB->sql_like('u.firstname', ':tifirst', false);



        $params['tifirst'] = $needle . '%';



    }



    if ($tilast !== '' || $silast !== '') {



        $needle = ($tilast !== '') ? $tilast : $silast;



        $where .= ' AND ' . $DB->sql_like('u.lastname', ':tilast', false);



        $params['tilast'] = $needle . '%';



    }



    if (!empty($useridfilter)) {



        $where .= ' AND e.userid = :userid';



        $params['userid'] = $useridfilter;



    }



} else {



    // Only own entries.



    $where .= ' AND e.userid = :me';



    $params['me'] = $USER->id;



}







// ---- Extra filters (apply before building SQL) ----



$allowedstatus = ['open','inprogress','done','unspecified','pendingreply','interview','shortlist','rejected','other'];



if ($filter_status !== '' && in_array($filter_status, $allowedstatus)) {



    $where .= ' AND e.status = :fstatus'; $params['fstatus'] = $filter_status;



}



$allowedcomm = ['email','online','letter','phone','inperson','other','unspecified'];



if ($filter_comm !== '' && in_array($filter_comm, $allowedcomm)) {



    $where .= ' AND e.commmethod = :fcomm'; $params['fcomm'] = $filter_comm;



}



$allowedtypes = ['appointment','contact','application','other'];



if ($filter_type !== '' && in_array($filter_type, $allowedtypes)) {



    $where .= ' AND e.entrytype = :ftype'; $params['ftype'] = $filter_type;



}



if ($filter_org !== '') {



    $where .= ' AND ' . $DB->sql_like('e.organisation', ':forg', false);



    $params['forg'] = '%' . $filter_org . '%';



}







$sql = 



$sql = "SELECT e.*, u.firstname, u.lastname, u.email



          FROM {bewerbungsdoku_entries} e



          $join



         WHERE $where



      ORDER BY " . (($canviewall && $mode === 'all') ? "u.lastname, u.firstname," : "") . " e.timecreated DESC, e.timemodified DESC";







// Extra filters



$allowedstatus = ['open','inprogress','done','unspecified','pendingreply','interview','shortlist','rejected','other'];



if ($canviewall && $mode === 'all') {



    if ($filter_status !== '' && in_array($filter_status, $allowedstatus)) { $where .= ' AND e.status = :fstatus'; $params['fstatus'] = $filter_status; }



    $allowedcomm = ['email','online','letter','phone','inperson','other','unspecified'];



    if ($filter_comm !== '' && in_array($filter_comm, $allowedcomm)) { $where .= ' AND e.commmethod = :fcomm'; $params['fcomm'] = $filter_comm; }



    $allowedtypes = ['appointment','contact','application','other'];



    if ($filter_type !== '' && in_array($filter_type, $allowedtypes)) { $where .= ' AND e.entrytype = :ftype'; $params['ftype'] = $filter_type; }



    if ($filter_org !== '') { $where .= ' AND ' . $DB->sql_like('e.organisation', ':forg', false); $params['forg'] = '%' . $filter_org . '%'; }



}







$entries = $DB->get_records_sql($sql, $params);



$fs = get_file_storage();







// CSV out.



$csv = new csv_export_writer();



$csv->set_filename('bewerbungsdokumentation');







// Header row.



$csv->add_data([



    get_string('csv_userfullname', 'mod_bewerbungsdoku'),



    get_string('csv_useremail', 'mod_bewerbungsdoku'),



    get_string('csv_type', 'mod_bewerbungsdoku'),



    get_string('csv_organisation', 'mod_bewerbungsdoku'),



    get_string('title', 'mod_bewerbungsdoku'),



    get_string('csv_createdat', 'mod_bewerbungsdoku'),



    get_string('csv_appliedat', 'mod_bewerbungsdoku'),



    get_string('status', 'mod_bewerbungsdoku'),



    get_string('notes', 'mod_bewerbungsdoku'),



    get_string('nextsteps', 'mod_bewerbungsdoku'),



    get_string('attachments', 'mod_bewerbungsdoku'),



]);







foreach ($entries as $e) {



    $fullname = fullname((object)['firstname' => $e->firstname, 'lastname' => $e->lastname]);



    // Collect attachment filenames.



    $files = $fs->get_area_files($context->id, 'mod_bewerbungsdoku', 'attachment', $e->id, 'filename', false);



    $filenames = [];



    foreach ($files as $file) { $filenames[] = $file->get_filename(); }



    $filelist = implode(' | ', $filenames);







    $csv->add_data([



        $fullname,



        $e->email,



        get_string('type_' . $e->entrytype, 'mod_bewerbungsdoku'),



        (string)$e->organisation,



        (string)$e->title,



        userdate($e->timecreated, '%Y-%m-%d %H:%M'),



        userdate($e->eventtime, '%Y-%m-%d'),



        get_string('status_' . $e->status, 'mod_bewerbungsdoku'),



        trim(html_to_text((string)$e->notes, 0)),



        trim(html_to_text((string)$e->nextsteps, 0)),



        $filelist,



    ]);



}







$csv->download_file();



exit;



