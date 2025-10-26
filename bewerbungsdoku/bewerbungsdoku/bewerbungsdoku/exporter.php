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


// PDF-Export im Kartenlayout (2 Spalten, Badges, Chips)

// Aufruf: /mod/bewerbungsdoku/exporter.php?id=CMID&status=&groupid=&participantid=&dateon=



require_once(__DIR__ . '/../../config.php');

require_once($CFG->libdir . '/pdflib.php');

require_once($CFG->dirroot . '/group/lib.php');



$id             = required_param('id', PARAM_INT);

$filter_status  = optional_param('status','',PARAM_ALPHA);

$filter_group   = optional_param('groupid', 0, PARAM_INT);

$filter_part    = optional_param('participantid', 0, PARAM_INT);

$dateon         = optional_param('dateon','',PARAM_RAW);



$cm = get_coursemodule_from_id('bewerbungsdoku', $id, 0, false, MUST_EXIST);

$instance = $DB->get_record('bewerbungsdoku', ['id'=>$cm->instance], '*', MUST_EXIST);

$course   = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);



require_login($course, true, $cm);

$context    = context_module::instance($cm->id);

$canviewall = has_capability('mod/bewerbungsdoku:viewallentries', $context);



// --- Daten holen ---

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

if ($canviewall && $filter_part > 0) {

    $where .= ' AND e.userid = :pid'; $params['pid'] = $filter_part;

}

if (!$canviewall) {

    $where .= ' AND e.userid = :me'; $params['me'] = $USER->id;

}

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

$records = $DB->get_records_sql($sql, $params);



// --- Helper: Badge-Label/Farbe ---

function bdd_badge(string $status): array {

    $map = [

        'pendingreply' => ['pendingreply', get_string('status_pendingreply','mod_bewerbungsdoku')],

        'interview'    => ['interview',    get_string('status_interview','mod_bewerbungsdoku')],

        'shortlist'    => ['shortlist',    get_string('status_shortlist','mod_bewerbungsdoku')],

        'rejected'     => ['rejected',     get_string('status_rejected','mod_bewerbungsdoku')],

        'open'         => ['open',         get_string('status_open','mod_bewerbungsdoku')],

        'inprogress'   => ['inprogress',   get_string('status_inprogress','mod_bewerbungsdoku')],

        'done'         => ['done',         get_string('status_done','mod_bewerbungsdoku')],

        'unspecified'  => ['other',        get_string('status_other','mod_bewerbungsdoku')],

        'other'        => ['other',        get_string('status_other','mod_bewerbungsdoku')],

    ];

    return $map[$status] ?? $map['other'];

}



// --- CSS laden ---

$css = '';

foreach ([__DIR__.'/pdf_styles.css', __DIR__.'/styles/pdf_styles.css'] as $p) {

    if (is_readable($p)) { $css = file_get_contents($p); break; }

}



/* ---------- Inline-Override (ohne deine CSS-Datei zu ändern) ---------- */

$cssfix = <<<CSS

/* Entfernt dicke Top-Linien (auch wenn pdf_styles.css sie setzt) */

table{ border-top:0 !important; }

tr:first-child td{ border-top:0 !important; }



/* Kopf-Tabelle der Karte ohne Außenrahmen; Badge-Zelle vertikal mittig */

table.head{ border:0 !important; }

table.head td{ border:0 !important; padding:0 !important; }

.badgecell{ width:1%; text-align:right; vertical-align:middle; }



/* echter Badge-Style (neutral) */

.badge{ display:inline-block; line-height:1; padding:4px 10px; border-radius:9999px; border:1px solid #e6eaf2; }



/* Status-Zeile in der KV-Tabelle NICHT als Pseudo-Badge stylen */

table.kv tr:nth-child(6) td:last-child,

table tr:nth-child(6) td:last-child{

  display:block !important; border:0 !important; background:transparent !important;

  color:#334155 !important; font-weight:normal !important; text-transform:none !important;

  letter-spacing:0 !important; padding:0 !important;

}



/* Sicherstellen, dass die KV-Tabelle den Kartenrahmen trägt (nicht die Kopf-Tabelle) */

.card .kv{ border:1px solid #dfe3ea !important; }

CSS;



// --- HTML aufbauen ---

$stand = userdate(time(), '%d.%m.%Y');

$title = get_string('pluginname','mod_bewerbungsdoku');



$fs = get_file_storage();

$cards = [];



foreach ($records as $e) {

    $fullname = fullname((object)['firstname'=>$e->firstname,'lastname'=>$e->lastname]);

    $email    = $e->email;

    $org      = trim((string)$e->organisation) === '' ? '—' : s($e->organisation);

    $role     = trim((string)$e->title)        === '' ? '—' : s($e->title);

    $when     = $e->eventtime ? userdate($e->eventtime, '%d.%m.%Y · %H:%M') : '—';

    [$badgecss,$badgelabel] = bdd_badge((string)$e->status);



    // Anhänge

    $filenames = [];

    foreach (['attachments','attachment','files','file'] as $area) {

        $stored = $fs->get_area_files($context->id, 'mod_bewerbungsdoku', $area, $e->id, 'filename', false);

        if ($stored) { foreach ($stored as $f) { $filenames[] = $f->get_filename(); } break; }

    }



    $card  = '<div class="card">';

    $card .=   '<table class="head" cellpadding="0" cellspacing="0"><tr>';

    $card .=     '<td class="title">'. $org .' · '. $role .'</td>';

    $card .=     '<td class="badgecell" valign="middle" align="right">'

           . '<span class="badge badge-'. $badgecss .'">'. s($badgelabel) .'</span>'

           . '</td>';



    $card .=   '</tr></table>';



    $card .=   '<table class="kv" cellpadding="0" cellspacing="0">';

    $card .=     '<tr><th>'.s('Vollständiger Name').'</th><td>'.s($fullname).'</td></tr>';

    $card .=     '<tr><th>'.s('E-Mail-Adresse').'</th><td><a href="mailto:'.s($email).'">'.s($email).'</a></td></tr>';

    $card .=     '<tr><th>'.s('Firma/Betrieb').'</th><td>'.$org.'</td></tr>';

    $card .=     '<tr><th>'.s('Beschäftigung als').'</th><td>'.$role.'</td></tr>';

    $card .=     '<tr><th>'.s('Datum').'</th><td>'.s($when).'</td></tr>';

    $card .=     '<tr><th>'.s('Status der Bewerbung').'</th><td>'.s($badgelabel).'</td></tr>';



    if (!empty($filenames)) {

        $card .=   '<tr><th>'.s('Anhänge').'</th><td><div class="chips">';

        foreach ($filenames as $fn) { $card .= '<span class="chip">'.s($fn).'</span>'; }

        $card .=   '</div></td></tr>';

    }

    $card .=   '</table>';

    $card .= '</div>';



    $cards[] = $card;

}



$html  = '<table class="header" cellpadding="0" cellspacing="0" width="100%"><tr>';

$html .= '<td class="title"><span class="title">'.s($title).'</span></td>';

$html .= '<td class="stand">Stand: '.s($stand).'</td>';

$html .= '</tr></table>';



if (empty($cards)) {

    $html .= '<div class="card"><table class="kv"><tr><th>'.s(get_string('noentries','mod_bewerbungsdoku')).'</th><td></td></tr></table></div>';

} else {

    $html .= '<table class="cards" cellpadding="0" cellspacing="0">';

    $max = count($cards);

    for ($i = 0; $i < $max; $i += 2) {

        $left  = $cards[$i];

        $right = $cards[$i+1] ?? '';



        $html .= '<tr>';

        $html .=   '<td class="cardcell">'.$left.'</td>';

        $html .=   '<td class="cardcell">'.$right.'</td>';

        $html .= '</tr>';



        if ($i + 2 < $max) {

            $html .= '<tr class="row-sep"><td class="row-sep-cell" colspan="2">&nbsp;</td></tr>';

        }

    }

    $html .= '</table>';

}

$html .= '<div class="footer">Dieses Layout ist für A4 optimiert.</div>';



// --- PDF ausgeben ---

class bdd_pdf extends pdf {

    public function Footer() {

        $this->SetY(-12);

        $this->SetFont('helvetica', '', 8);

        $this->Cell(0, 8,

            userdate(time(), '%Y-%m-%d %H:%M').'  |  '.get_string('page').' '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(),

            0, 0, 'R'

        );

    }

}

$pdf = new bdd_pdf();

$pdf->SetCreator('Moodle');

$pdf->SetAuthor('mod_bewerbungsdoku');

$pdf->SetTitle($title);

$pdf->SetPrintHeader(false);

$pdf->SetPrintFooter(true);

$pdf->SetMargins(15, 18, 15);

$pdf->AddPage();

$pdf->SetFont('helvetica', '', 11);



/* wichtig: cssfix NACH $css anhängen, damit !important greift */

$pdf->writeHTML('<style>'.$css.$cssfix.'</style>'.$html, true, false, true, false, '');



$pdf->Output('bewerbungsdokumentation.pdf', 'I');

exit;