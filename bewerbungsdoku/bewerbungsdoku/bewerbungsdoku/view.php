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


// Bewerbungsdoku - Ansicht mit Karten, kompaktem Filter & Auswahlmodus


// ----------------------------------------------------------------------


require_once(__DIR__ . '/../../config.php');


require_once($CFG->dirroot.'/group/lib.php');





$id = optional_param('id', 0, PARAM_INT); // Course module id


$n  = optional_param('n', 0, PARAM_INT);  // Instance id (fallback)





if ($id) {


    $cm = get_coursemodule_from_id('bewerbungsdoku', $id, 0, false, MUST_EXIST);


    $instance = $DB->get_record('bewerbungsdoku', ['id'=>$cm->instance], '*', MUST_EXIST);


    $course   = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);


} elseif ($n) {


    $instance = $DB->get_record('bewerbungsdoku', ['id'=>$n], '*', MUST_EXIST);


    $course   = $DB->get_record('course', ['id'=>$instance->course], '*', MUST_EXIST);


    $cm = get_coursemodule_from_instance('bewerbungsdoku', $instance->id, $course->id, false, MUST_EXIST);


} else {


    print_error('missingparameter');


}


require_login($course, true, $cm);


$context     = context_module::instance($cm->id);


require_capability('mod/bewerbungsdoku:view', $context);





$canviewall  = has_capability('mod/bewerbungsdoku:viewallentries', $context);


$canexport   = has_capability('mod/bewerbungsdoku:exportentries', $context);


$cansubmit   = has_capability('mod/bewerbungsdoku:submit', $context);





// ---------------- Filter ----------------


$filter_status      = optional_param('status','',PARAM_ALPHA);


$filter_group       = optional_param('groupid', 0, PARAM_INT);


$filter_participant = optional_param('participantid', 0, PARAM_INT);


$dateon             = optional_param('dateon','',PARAM_RAW);





$PAGE->set_url('/mod/bewerbungsdoku/view.php', ['id'=>$cm->id]);


$PAGE->set_title(format_string($instance->name));


$PAGE->set_heading($course->fullname);





// CSS einbinden


$PAGE->requires->css(new moodle_url('/mod/bewerbungsdoku/styles.css'));





echo $OUTPUT->header();


echo html_writer::start_div('', ['id'=>'page-mod-bewerbungsdoku-view']);





// ---------------- Filterleiste ----------------


$filterurl = new moodle_url('/mod/bewerbungsdoku/view.php', ['id'=>$cm->id]);


echo html_writer::start_tag('form', ['method'=>'get','action'=>$filterurl,'class'=>'mform mb-3 bdd-filter','id'=>'bdd-filter-form']);


echo html_writer::empty_tag('input', ['type'=>'hidden','name'=>'id','value'=>$cm->id]);


echo html_writer::start_div('row');





// Status


echo html_writer::start_div('col-auto');


echo html_writer::tag('label', get_string('status','mod_bewerbungsdoku'), ['for'=>'id_status','class'=>'form-label']);


echo html_writer::start_tag('select', ['name'=>'status','id'=>'id_status','class'=>'custom-select']);


$statusopts = [


    ''              => '-- '.get_string('status','mod_bewerbungsdoku').' --',


    'pendingreply'  => get_string('status_pendingreply','mod_bewerbungsdoku'),


    'interview'     => get_string('status_interview','mod_bewerbungsdoku'),


    'shortlist'     => get_string('status_shortlist','mod_bewerbungsdoku'),


    'rejected'      => get_string('status_rejected','mod_bewerbungsdoku'),


    'open'          => get_string('status_open','mod_bewerbungsdoku'),


    'inprogress'    => get_string('status_inprogress','mod_bewerbungsdoku'),


    'done'          => get_string('status_done','mod_bewerbungsdoku'),


    'other'         => get_string('status_other','mod_bewerbungsdoku'),


];


foreach ($statusopts as $v=>$l) {


    echo html_writer::tag('option',$l,['value'=>$v]+($filter_status===$v?['selected'=>'selected']:[]));


}


echo html_writer::end_tag('select');


echo html_writer::end_div();





// Gruppen (sichtbar nur für Trainer/Admin – Teilnehmer sehen keine Auswahl)


if ($canviewall) {


    echo html_writer::start_div('col-auto');


    echo html_writer::tag('label', get_string('groups','group'), ['for'=>'id_groupid','class'=>'form-label']);


    echo html_writer::start_tag('select', ['name'=>'groupid','id'=>'id_groupid','class'=>'custom-select']);


    echo html_writer::tag('option', get_string('allgroups_local','mod_bewerbungsdoku'), ['value'=>0]);


    $groups = groups_get_all_groups($course->id);


    if ($groups) {


        foreach ($groups as $g) {


            echo html_writer::tag('option', format_string($g->name),


                ['value'=>$g->id]+((int)$filter_group===(int)$g->id?['selected'=>'selected']:[])


            );


        }


    }


    echo html_writer::end_tag('select');


    echo html_writer::end_div();


}





// Teilnehmer/in (nur wenn Gruppe gewählt und canviewall)


if ($canviewall && $filter_group > 0) {


    $members = groups_get_members($filter_group, 'u.id, u.firstname, u.lastname', 'lastname ASC, firstname ASC');


    echo html_writer::start_div('col-auto');


    $label = 'Teilnehmer/in der Gruppe';


    echo html_writer::tag('label', $label, ['for'=>'id_participantid','class'=>'form-label']);


    echo html_writer::start_tag('select', ['name'=>'participantid','id'=>'id_participantid','class'=>'custom-select']);


    echo html_writer::tag('option','-- Alle --',['value'=>0]);


    if ($members) {


        foreach ($members as $u) {


            echo html_writer::tag('option', fullname($u),


                ['value'=>$u->id]+((int)$filter_participant===(int)$u->id?['selected'=>'selected']:[])


            );


        }


    }


    echo html_writer::end_tag('select');


    echo html_writer::end_div();


}





// Datum


echo html_writer::start_div('col-auto');


$datelabel = 'Datum (wann haben Sie sich beworben)';


echo html_writer::tag('label', $datelabel, ['for'=>'id_dateon','class'=>'form-label']);


echo html_writer::empty_tag('input', ['type'=>'date','name'=>'dateon','id'=>'id_dateon','value'=>$dateon,'class'=>'form-control']);


echo html_writer::end_div();





// Buttons


echo html_writer::start_div('col-auto bdd-filter__cell--buttons');


echo html_writer::tag('button', get_string('filter'), ['type'=>'submit','class'=>'btn btn-secondary']);


$reseturl = new moodle_url('/mod/bewerbungsdoku/view.php', ['id'=>$cm->id]);


echo html_writer::link($reseturl, get_string('reset'), ['class'=>'btn btn-secondary']);


echo html_writer::end_div();





echo html_writer::end_div(); // row


echo html_writer::end_tag('form');





// ---------------- Daten holen ----------------


$params = ['bid'=>$instance->id];


$where  = 'e.bewerbungsdokuid = :bid';


$join   = 'JOIN {user} u ON u.id = e.userid';





$allowedstatus = ['open','inprogress','done','unspecified','pendingreply','interview','shortlist','rejected','other'];


if ($filter_status !== '' && in_array($filter_status, $allowedstatus)) {


    $where .= ' AND e.status = :st'; $params['st'] = $filter_status;


}


if ($canviewall && $filter_group > 0) {


    $join  .= ' JOIN {groups_members} gm ON gm.userid = e.userid';


    $where .= ' AND gm.groupid = :gid'; $params['gid'] = $filter_group;


}


if ($canviewall && $filter_participant > 0) {


    $where .= ' AND e.userid = :pid'; $params['pid'] = $filter_participant;


}


if (!$canviewall) { $where .= ' AND e.userid = :me'; $params['me'] = $USER->id; }





if (!empty($dateon)) {


    $fromts = strtotime($dateon.' 00:00:00'); $tots = strtotime($dateon.' 23:59:59');


    if ($fromts && $tots) {


        $where .= ' AND e.timecreated >= :fromts AND e.timecreated <= :tots';


        $params['fromts']=$fromts; $params['tots']=$tots;


    }


}





$sql = "SELECT e.*, u.firstname, u.lastname, u.email


          FROM {bewerbungsdoku_entries} e


          $join


         WHERE $where


      ORDER BY e.eventtime DESC, e.timemodified DESC";


$entries = $DB->get_records_sql($sql, $params);





// ---------------- Karten-Grid ----------------


echo html_writer::start_div('bdd-grid');





if (!$entries) {


    echo html_writer::div(get_string('noentries','mod_bewerbungsdoku'),'alert alert-info');


} else {


    foreach ($entries as $e) {


        $statuskey = in_array($e->status,$allowedstatus) ? $e->status : 'other';


        $name   = fullname((object)['firstname'=>$e->firstname,'lastname'=>$e->lastname]);


        $email  = s($e->email);


        $created= userdate($e->timecreated, '%d.%m.%Y, %H:%M');


        $applied= userdate($e->eventtime,   '%d.%m.%Y, %H:%M');


        $org    = s($e->organisation);


        $role   = s($e->title);





        $detailurl = new moodle_url('/mod/bewerbungsdoku/details.php', ['id'=>$cm->id,'entryid'=>$e->id]);





        echo html_writer::start_div('bdd-card bdd-card--click', ['data-href'=>$detailurl->out(false)]);


            echo html_writer::div('↗','bdd-linkicon'); // Klick-Hinweis





            echo html_writer::start_div('bdd-card__head');


                echo html_writer::tag('h3', s($name).' | '.html_writer::span($email,'bdd-mail'), ['class'=>'bdd-title']);


                echo html_writer::start_div('bdd-head__right');


                    echo html_writer::tag('span', get_string('status_'.$statuskey,'mod_bewerbungsdoku'),


                        ['class'=>'bd-status bd-status--'.$statuskey]);


                    echo html_writer::empty_tag('input', [


                        'type'=>'radio','name'=>'pickid','value'=>$e->id,


                        'class'=>'bdd-pick','title'=>'Eintrag auswählen'


                    ]);


                echo html_writer::end_div();


            echo html_writer::end_div();





            echo html_writer::start_div('bdd-meta');


                echo html_writer::tag('div', html_writer::span('Erstellt:','bdd-meta__label').' '.$created, ['class'=>'bdd-meta__item']);


                echo html_writer::tag('div', html_writer::span('Bewerbung:','bdd-meta__label').' '.$applied, ['class'=>'bdd-meta__item']);


                echo html_writer::tag('div', html_writer::span('Firma/Betrieb:','bdd-meta__label').' '.$org, ['class'=>'bdd-meta__item']);


                echo html_writer::tag('div', html_writer::span('Beschäftigung als:','bdd-meta__label').' '.$role, ['class'=>'bdd-meta__item']);


            echo html_writer::end_div();





        echo html_writer::end_div();


    }


}


echo html_writer::end_div(); // grid





// ---------------- Aktionsleiste ----------------


$bar = html_writer::start_div('bdd-bottombar mt-3');


if ($cansubmit) {


    $bar .= $OUTPUT->single_button(new moodle_url('/mod/bewerbungsdoku/add.php',['id'=>$cm->id]), get_string('addentry','mod_bewerbungsdoku'), 'post');


}





$exportparams = [


    'id'=>$cm->id,


    'status'=>$filter_status,


    'groupid'=>$filter_group,


    'participantid'=>$filter_participant,


    'dateon'=>$dateon


];


if ($canexport) {


    $bar .= $OUTPUT->single_button(new moodle_url('/mod/bewerbungsdoku/export.php',$exportparams), get_string('exportcsv','mod_bewerbungsdoku'), 'get');


} else {


    $bar .= $OUTPUT->single_button(new moodle_url('/mod/bewerbungsdoku/exportpdf.php',['id'=>$cm->id]), get_string('exportpdf','mod_bewerbungsdoku'), 'get');


}


$bar .= html_writer::tag('button','Bearbeiten', ['type'=>'button','class'=>'btn btn-secondary','id'=>'bdd-start-edit']);


$bar .= html_writer::tag('button','Löschen',    ['type'=>'button','class'=>'btn btn-secondary','id'=>'bdd-start-del']);


$bar .= html_writer::tag('button','Ausgewählten Eintrag bearbeiten', ['type'=>'button','class'=>'btn btn-primary d-none','id'=>'bdd-do-edit','disabled'=>'disabled']);


$bar .= html_writer::tag('button','Ausgewählten Eintrag löschen',    ['type'=>'button','class'=>'btn btn-danger  d-none','id'=>'bdd-do-del','disabled'=>'disabled']);


$bar .= html_writer::tag('button','Abbrechen', ['type'=>'button','class'=>'btn btn-link d-none','id'=>'bdd-cancel']);


$bar .= html_writer::end_div();


echo $bar;





echo '<div style="font-size:9pt;color:#6b7280;margin-top:8px;">'


   . '<strong>Entwickler</strong>: Husam Afandi | '


   . '<strong>Kontakt</strong>: <a href="mailto:support.moodle@weidinger.com" style="color:#6b7280;">Ticket an den Support schicken</a>'


   . '</div>';








// ---------------- JS: Karten klickbar + Auswahlmodus ----------------


$js = <<<JS


(function(){


  // Karte klickbar


  document.querySelectorAll('.bdd-card.bdd-card--click').forEach(function(card){


    card.addEventListener('click', function(e){


      // Klicks auf Inputs nicht abfangen (Picker)


      if (e.target && (e.target.tagName==='INPUT' || e.target.closest('input'))) return;


      var href = card.getAttribute('data-href');


      if(href){ window.location.href = href; }


    });


  });





  // Auswahlmodus


  var root    = document.getElementById('page-mod-bewerbungsdoku-view') || document.body;


  var grid    = document.querySelector('.bdd-grid');


  var btnStartEdit = document.getElementById('bdd-start-edit');


  var btnStartDel  = document.getElementById('bdd-start-del');


  var btnDoEdit    = document.getElementById('bdd-do-edit');


  var btnDoDel     = document.getElementById('bdd-do-del');


  var btnCancel    = document.getElementById('bdd-cancel');





  function enterPick(which){


    root.classList.add('bdd-picking');


    btnStartEdit.classList.add('d-none');


    btnStartDel.classList.add('d-none');


    btnCancel.classList.remove('d-none');


    btnDoEdit.classList.toggle('d-none', which!=='edit');


    btnDoDel.classList.toggle('d-none',  which!=='del');


    btnDoEdit.disabled = true; btnDoDel.disabled = true;





    grid.querySelectorAll('input.bdd-pick').forEach(function(r){


      r.checked = false;


      r.addEventListener('change', function(){


        if (which==='edit') btnDoEdit.disabled = !r.checked;


        if (which==='del')  btnDoDel.disabled  = !r.checked;


      }, {once:false});


    });


  }


  function exitPick(){


    root.classList.remove('bdd-picking');


    btnStartEdit.classList.remove('d-none');


    btnStartDel.classList.remove('d-none');


    btnCancel.classList.add('d-none');


    btnDoEdit.classList.add('d-none'); btnDoEdit.disabled = true;


    btnDoDel.classList.add('d-none');  btnDoDel.disabled  = true;


    grid.querySelectorAll('input.bdd-pick').forEach(function(r){ r.checked = false; });


  }


  function goTo(action){


    var picked = grid.querySelector('input.bdd-pick:checked');


    if(!picked){ return; }


    var entryid = picked.value;


    var base = M.cfg.wwwroot + '/mod/bewerbungsdoku/' + action + '.php';


    var url  = new URL(base, window.location.origin);


    url.searchParams.set('id', %d);


    url.searchParams.set('entryid', entryid);


    if(action==='delete'){ url.searchParams.set('sesskey', M.cfg.sesskey); }


    window.location.href = url.toString();


  }





  btnStartEdit && btnStartEdit.addEventListener('click', function(){ enterPick('edit'); });


  btnStartDel  && btnStartDel.addEventListener('click',  function(){ enterPick('del');  });


  btnCancel    && btnCancel.addEventListener('click',     exitPick);


  btnDoEdit    && btnDoEdit.addEventListener('click',     function(){ goTo('edit');   });


  btnDoDel     && btnDoDel.addEventListener('click',      function(){ goTo('delete'); });


})();


JS;


$js = sprintf($js, (int)$cm->id);


echo html_writer::script($js);





echo html_writer::end_div(); // page id wrapper


echo html_writer::script("\n// --- Make cards clickable to open details (decode &amp; -> &) ---\n(function(){\n  var cards = document.querySelectorAll('.bdd-card--click');\n  cards.forEach(function(card){\n    card.style.cursor = 'pointer';\n    card.addEventListener('click', function(e){\n      if (document.body.classList.contains('bdd-picking')) return;\n      if (e.target.closest('a,button,.btn,input,select,textarea,.bdd-pick')) return;\n      var href = card.getAttribute('data-href') || '';\n      if (href){\n        href = href.replace(/&amp;/g,'&');\n        window.location.assign(href);\n      }\n    });\n  });\n})();\n");





echo $OUTPUT->footer();


