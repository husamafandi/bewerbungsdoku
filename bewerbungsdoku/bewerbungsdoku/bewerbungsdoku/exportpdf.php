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

// exportpdf.php – klickbare Anhang-Links (pluginfile.php)
// behält alle bisherigen Fixes/Features

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/pdflib.php');

// ----- Eingabeparameter
$id       = required_param('id', PARAM_INT);
$tifirst  = optional_param('tifirst', '', PARAM_RAW);
$tilast   = optional_param('tilast',  '', PARAM_RAW);
$sifirst  = optional_param('sifirst', '', PARAM_RAW);
$silast   = optional_param('silast',  '', PARAM_RAW);
$datefrom = optional_param('datefrom', '', PARAM_RAW);
$dateto   = optional_param('dateto',   '', PARAM_RAW);
$dateon   = optional_param('dateon',   '', PARAM_RAW);
$filter_status = optional_param('status','',PARAM_ALPHA);
$filter_comm   = optional_param('commmethod','',PARAM_ALPHA);
$filter_type   = optional_param('entrytype','',PARAM_ALPHA);
$filter_org    = optional_param('organisation','',PARAM_RAW_TRIMMED);
$filter_group  = optional_param('groupid',0,PARAM_INT);

// ----- Kontext laden
$cm       = get_coursemodule_from_id('bewerbungsdoku', $id, 0, false, MUST_EXIST);
$instance = $DB->get_record('bewerbungsdoku', ['id' => $cm->instance], '*', MUST_EXIST);
$course   = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_login($course, true, $cm);
$context    = context_module::instance($cm->id);
$canviewall = has_capability('mod/bewerbungsdoku:viewallentries', $context);

// ----- Debugausgabe im PDF unterdrücken
$__prevdebugdisplay = isset($CFG->debugdisplay) ? $CFG->debugdisplay : 0;
$CFG->debugdisplay = 0;

// ----- SQL & Filter
$params = ['bid' => $instance->id];
$where  = 'e.bewerbungsdokuid = :bid';
$join   = 'JOIN {user} u ON u.id = e.userid';

if ($filter_group > 0) {
    $join  .= ' JOIN {groups_members} gm ON gm.userid = e.userid
                JOIN {groups} g ON g.id = gm.groupid';
    $where .= ' AND g.courseid = :gcourseid AND gm.groupid = :gid';
    $params['gcourseid'] = $course->id; $params['gid'] = $filter_group;
}
if (!empty($datefrom) && ($ts = strtotime($datefrom.' 00:00:00'))) { $where .= ' AND e.timecreated >= :fromts'; $params['fromts'] = $ts; }
if (!empty($dateto)   && ($ts = strtotime($dateto.' 23:59:59')))   { $where .= ' AND e.timecreated <= :tots';   $params['tots']   = $ts; }

if ($canviewall) {
    if ($tifirst !== '' || $sifirst !== '') {
        $needle = ($tifirst !== '') ? $tifirst : $sifirst;
        $where .= ' AND '.$DB->sql_like('u.firstname', ':tifirst', false);
        $params['tifirst'] = $needle.'%';
    }
    if ($tilast !== '' || $silast !== '') {
        $needle = ($tilast !== '') ? $tilast : $silast;
        $where .= ' AND '.$DB->sql_like('u.lastname', ':tilast', false);
        $params['tilast'] = $needle.'%';
    }
} else {
    $where .= ' AND e.userid = :me';
    $params['me'] = $USER->id;
}

$allowedstatus = ['open','inprogress','done','unspecified','pendingreply','interview','shortlist','rejected','other'];
if ($filter_status !== '' && in_array($filter_status,$allowedstatus)) { $where .= ' AND e.status = :fstatus'; $params['fstatus'] = $filter_status; }
$allowedcomm = ['email','online','letter','phone','inperson','other','unspecified'];
if ($filter_comm !== '' && in_array($filter_comm,$allowedcomm)) { $where .= ' AND e.commmethod = :fcomm'; $params['fcomm'] = $filter_comm; }
$allowedtypes = ['appointment','contact','application','other'];
if ($filter_type !== '' && in_array($filter_type,$allowedtypes)) { $where .= ' AND e.entrytype = :ftype'; $params['ftype'] = $filter_type; }
if ($filter_org !== '') { $where .= ' AND '.$DB->sql_like('e.organisation', ':forg', false); $params['forg'] = '%'.$filter_org.'%'; }

// Namefelder für fullname()
$namefields = "u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename";

$sql = "SELECT e.*, $namefields, u.email
          FROM {bewerbungsdoku_entries} e
          $join
         WHERE $where
      ORDER BY ".($canviewall ? "u.lastname, u.firstname," : "")." e.timecreated DESC, e.timemodified DESC";

$entries = $DB->get_records_sql($sql, $params);

// ----- PDF initialisieren
$pdf = new pdf();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// Font-Fallback
$FONT = (function() use ($CFG) {
    $base = $CFG->libdir . '/tcpdf/fonts/';
    foreach (['dejavusans','freesans','freeserif'] as $f) {
        if (file_exists($base.$f.'.php')) { return $f; }
    }
    return 'helvetica';
})();
$pdf->SetFontSubsetting(true);

// Kopf
draw_global_header($pdf, $FONT, $instance->name);

// Layout
$margins = $pdf->getMargins();
$pageW   = $pdf->getPageWidth();
$lm      = $margins['left']  ?? 15;
$rm      = $margins['right'] ?? 15;
$usableW = max(1, $pageW - $lm - $rm);

$padding = 5.0;
$cardW   = $usableW;
$x       = $lm;
$y       = $pdf->GetY() + 4;
$limitY  = $pdf->getPageHeight() - $pdf->getBreakMargin();

$BORDER   = [229,231,235];
$VALUECOL = '#334155';

/* --------- Status / Badge --------- */
function status_text_map($status) {
    $map = [
        'pendingreply' => 'Antwort des Betriebes offen',
        'open'         => 'Antwort des Betriebes offen',
        'interview'    => 'Vorstelltermin',
        'shortlist'    => 'in Vormerkung',
        'rejected'     => 'Absage',
        'other'        => 'Sonstiges',
        'done'         => 'Erledigt',
        'inprogress'   => 'In Bearbeitung',
        'unspecified'  => '—',
    ];
    return $map[strtolower($status ?? '')] ?? (string)$status;
}
function badge_conf($status){
    $s = strtolower($status ?? '');
    $map = [
        'pendingreply' => ['ANTWORT DES BETRIEBES OFFEN',[255,247,237],[255,237,213],[154,52,18]],
        'open'         => ['ANTWORT DES BETRIEBES OFFEN',[255,247,237],[255,237,213],[154,52,18]],
        'interview'    => ['VORSTELLTERMIN',              [236,252,203],[217,249,157],[54,83,20]],
        'shortlist'    => ['IN VORMERKUNG',               [224,242,254],[186,230,253],[7,89,133]],
        'rejected'     => ['ABSAGE',                      [254,226,226],[254,202,202],[127,29,29]],
        'other'        => ['SONSTIGES',                   [241,245,249],[226,232,240],[51,65,85]],
        'done'         => ['ERLEDIGT',                    [236,252,203],[217,249,157],[54,83,20]],
        'inprogress'   => ['IN BEARBEITUNG',              [241,245,249],[226,232,240],[51,65,85]],
    ];
    return $map[$s] ?? $map['other'];
}

/* --------- Badge zeichnen --------- */
function draw_badge(TCPDF $pdf, $xRight, $yTop, $text, array $fill, array $border, array $txt, $font) {
    $fsz   = 9; $padX  = 4; $pillH = 6.8; $r = 3.4;
    $pdf->SetFont($font, 'B', $fsz);
    $w = max(0.1, $pdf->GetStringWidth($text) + 2*$padX);
    $x = $xRight - $w;

    $pdf->SetDrawColor($border[0],$border[1],$border[2]);
    $pdf->SetFillColor($fill[0],$fill[1],$fill[2]);
    $pdf->RoundedRect($x, $yTop, $w, $pillH, $r, '1111', 'DF');
    $pdf->SetTextColor($txt[0],$txt[1],$txt[2]);
    $pdf->MultiCell($w, $pillH, $text, 0, 'C', 0, 0, $x, $yTop, true, 0, false, true, 0, 'M', true);
}

/* --------- Globaler Seitenkopf (Logo + Titel) --------- */
function draw_global_header(TCPDF $pdf, $FONT, $title) {
    $margins = $pdf->getMargins();
    $lm = $margins['left'] ?? 15;  $rm = $margins['right'] ?? 15;
    $pageW = $pdf->getPageWidth();
    $usableW = max(1, $pageW - $lm - $rm);
    $y0 = $pdf->GetY();

    $candidates = [
        __DIR__ . '/pix/logo.png',
        __DIR__ . '/pix/logo.jpg',
        dirname(__DIR__) . '/pix/logo.png',
        dirname(__DIR__) . '/pix/logo.jpg',
        (isset($GLOBALS['CFG']->dirroot) ? $GLOBALS['CFG']->dirroot : '') . '/mod/bewerbungsdoku/pix/logo.png',
        (isset($GLOBALS['CFG']->dirroot) ? $GLOBALS['CFG']->dirroot : '') . '/mod/bewerbungsdoku/pix/logo.jpg',
    ];
    $logo = '';
    foreach ($candidates as $cand) {
        if ($cand && file_exists($cand) && is_readable($cand)) { $logo = $cand; break; }
    }

    $logoW   = 18;   // mm
    $logoH   = 18;   // mm (Reservefläche)
    $logoPad = 1.0;  // mm
    $dpi     = 96;   $pxmm = 25.4 / max(1,$dpi);
    $radmm   = $pxmm; // ≈ 1px Ecke
    $borderW = 0.10;  // sehr dünn

    if ($logo) {
        try {
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetDrawColor(229, 231, 235);
            $pdf->SetLineWidth($borderW);
            $pdf->RoundedRect($lm, $y0 - 5, $logoW, $logoH, $radmm, '1111', 'DF');

            $w = max(0.1, $logoW - 2*$logoPad);
            $pdf->Image($logo, $lm + $logoPad, $y0 - 5 + $logoPad, $w, 0, '', '', '', false, 300);
        } catch (Throwable $t) {
            // Logo überspringen (PDF trotzdem erzeugen)
        }
    }

    // Datum rechts
    $pdf->SetFont($FONT,'',9);
    $pdf->SetTextColor(100,116,139);
    $pdf->SetXY($lm, $y0);
    $pdf->Cell($usableW, 0, 'Stand: ' . userdate(time(), '%d.%m.%Y'), 0, 0, 'R');

    // Titel zentriert
    $pdf->SetFont($FONT,'B',11);
    $pdf->SetTextColor(15,23,42);
    $pdf->SetXY($lm, $y0);
    $pdf->Cell($usableW, 0, format_string($title), 0, 1, 'C');

    $after = max($pdf->GetY() + 6, $y0 - 5 + $logoH);
    $pdf->SetY($after);
    return $after;
}

/* --------- Key-Value Zeile --------- */
function kv_row($label,$value,$first=false,$labelw='42%',$valuecol='#334155') {
    $top = $first ? '0' : '1px solid #e5e7eb';
    return '<tr>
      <td width="'.$labelw.'" style="color:#64748b;font-weight:700;font-size:9.5pt;background:#f7f9fc;border-top:'.$top.';border-right:1px solid #e5e7eb;">'.$label.'</td>
      <td style="border-top:'.$top.';font-size:10pt;color:'.$valuecol.'">'.$value.'</td>
    </tr>';
}

/* --------- Karte --------- */
function draw_card(TCPDF $pdf, $x, $y, $w, $htmlBody, $headerLeft, $badgeConf, $font, $padding, &$contentH, array $BORDER) {
    $pdf->SetFont($font,'B',11);
    $headerTextH = $pdf->getStringHeight($w - 2*$padding, $headerLeft, false, true, '', 0);
    $headerH     = max(8.0, $headerTextH);
    $pillH       = 6.8;

    // Messlauf
    $pdf->startTransaction();
    $pdf->SetXY($x+$padding, $y+$padding);
    $pdf->Cell($w-2*$padding, 0, $headerLeft, 0, 1, 'L', 0);

    $pdf->SetDrawColor(229,231,235);
    $pdf->SetLineWidth(0.2);
    $pdf->SetXY($x+$padding, $y+$padding+$headerH+1.5);
    $pdf->SetFont($font,'',10);
    $pdf->writeHTMLCell(
        $w-2*$padding, 0,
        $x+$padding, $y+$padding+$headerH+1.5,
        '<table width="100%" cellpadding="4" cellspacing="0" border="0" style="border-collapse:separate;border-spacing:1pt"><tbody>'.$htmlBody.'</tbody></table>',
        0, 1, 0, true, '', true
    );
    $contentH = $pdf->GetY() - ($y+$padding+$headerH+1.5);
    $pdf->rollbackTransaction(true);

    $h = $padding + $headerH + 1.5 + $contentH + $padding;

    // Rahmen
    $pdf->SetDrawColor($BORDER[0],$BORDER[1],$BORDER[2]);
    $pdf->SetLineWidth(0.2);
    $pdf->SetFillColor(255,255,255);
    $pdf->RoundedRect($x, $y, $w, $h, 4, '1111', 'D');

    // Schreiblauf
    $pdf->SetXY($x+$padding, $y+$padding);
    $pdf->SetFont($font,'B',11);
    $pdf->SetTextColor(15,23,42);
    $pdf->Cell($w-2*$padding, 0, $headerLeft, 0, 1, 'L', 0);

    $pdf->SetDrawColor(229,231,235);
    $pdf->SetLineWidth(0.2);
    $pdf->SetXY($x+$padding, $y+$padding+$headerH+1.5);
    $pdf->SetFont($font,'',10);
    $pdf->writeHTMLCell(
        $w-2*$padding, 0,
        $x+$padding, $y+$padding+$headerH+1.5,
        '<table width="100%" cellpadding="4" cellspacing="0" border="0" style="border-collapse:separate;border-spacing:1pt"><tbody>'.$htmlBody.'</tbody></table>',
        0, 1, 0, true, '', true
    );

    // Eraser (erste Tabellenlinie übermalen)
    $pdf->SetFillColor(255,255,255);
    $yTableTop = $y + $padding + $headerH + 1.5;
    $pdf->Rect($x + $padding - 0.6, $yTableTop - 1.2, $w - 2*$padding + 1.2, 2.4, 'F');

    if ($badgeConf) {
        [$txt,$fill,$bd,$txtcol] = $badgeConf;
        $yBadge = $y + $padding + ($headerH - $pillH)/2;
        draw_badge($pdf, $x + $w - $padding, $yBadge, $txt, $fill, $bd, $txtcol, $font);
    }
    return $h;
}

// ----- Rendern
$pdf->SetFont($FONT,'',10);
$fs = get_file_storage();

foreach ($entries as $e) {
    // Vollständiger Name robust
    $fullname = (function($u){
        if (function_exists('fullname')) {
            try { return fullname($u); } catch (Throwable $t) { return trim(($u->firstname ?? '').' '.($u->lastname ?? '')); }
        }
        return trim(($u->firstname ?? '').' '.($u->lastname ?? ''));
    })($e);
    $datecreated = userdate($e->timecreated, '%d.%m.%Y · %H:%M');

    // Bewerbungsdatum aus Formular
    $app_ts = 0;
    if (isset($e->eventtime) && $e->eventtime) {
        $app_ts = (int)$e->eventtime;
    } else if (isset($e->dateon) && $e->dateon) {
        $app_ts = (int)$e->dateon;
    }
    $application = $app_ts ? userdate($app_ts, '%d.%m.%Y · %H:%M') : '—';
    $applabel = 'Datum (wann haben Sie sich beworben)';

    // Anhänge als klickbare Links (pluginfile.php)
    $files = $fs->get_area_files($context->id, 'mod_bewerbungsdoku', 'attachment', $e->id, 'filename', false);
    $items = [];
    foreach ($files as $file) {
        /** @var stored_file $file */
        $url = moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        )->out(false);
        $items[] = '<li style="margin:2px 0"><a href="'.$url.'">'.s($file->get_filename()).'</a></li>';
    }
    $attlist = $items ? '<ul style="margin:0; padding-left:12px; line-height:1.5">'.implode('', $items).'</ul>' : '—';

    $statustext = status_text_map($e->status);

    $body = '
        '.kv_row(get_string('csv_userfullname','mod_bewerbungsdoku'), s($fullname), true,'42%',$VALUECOL).'
        '.kv_row(get_string('csv_useremail','mod_bewerbungsdoku'), s($e->email), false,'42%',$VALUECOL).'
        '.kv_row(get_string('organisation','mod_bewerbungsdoku'), s($e->organisation), false,'42%',$VALUECOL).'
        '.kv_row(get_string('title','mod_bewerbungsdoku'),        s($e->title), false,'42%',$VALUECOL).'
        '.kv_row(get_string('createdat','mod_bewerbungsdoku'),    $datecreated, false,'42%',$VALUECOL).'
        '.kv_row($applabel, $application, false,'42%',$VALUECOL).'
        '.kv_row(get_string('status','mod_bewerbungsdoku'),       s($statustext), false,'42%',$VALUECOL).'
        '.kv_row(get_string('notes','mod_bewerbungsdoku'),        s(html_to_text((string)$e->notes,0)), false,'42%',$VALUECOL).'
        '.kv_row(get_string('nextsteps','mod_bewerbungsdoku'),    s(html_to_text((string)$e->nextsteps,0)), false,'42%',$VALUECOL).'
        <tr>
          <td width="42%" style="color:#64748b;font-weight:700;font-size:9.5pt;background:#f7f9fc;border-top:1px solid #e5e7eb;border-right:1px solid #e5e7eb;">'.get_string('attachments','mod_bewerbungsdoku').'</td>
          <td style="border-top:1px solid #e5e7eb;color:'.$VALUECOL.';vertical-align:top">'.$attlist.'</td>
        </tr>';

    $header = s($e->organisation).' · '.s($e->title);
    $badge  = badge_conf($e->status);

    if ($y + 80 > $limitY) {
        $pdf->AddPage();
        draw_global_header($pdf, $FONT, $instance->name);
        $pdf->SetFont($FONT,'',10);
        $y = $pdf->GetY() + 4;
        $limitY = $pdf->getPageHeight() - $pdf->getBreakMargin();
    }

    $contentH = 0;
    $cardH = draw_card($pdf, $x, $y, $cardW, $body, $header, $badge, $FONT, $padding, $contentH, $BORDER);
    $y += $cardH + 8;

    if ($y + 40 > $limitY) {
        $pdf->AddPage();
        draw_global_header($pdf, $FONT, $instance->name);
        $pdf->SetFont($FONT,'',10);
        $y = $pdf->GetY() + 4;
        $limitY = $pdf->getPageHeight() - $pdf->getBreakMargin();
    }
}

// ----- Download
$filename = clean_filename('bewerbungsdokumentation.pdf');
while (ob_get_level()) { @ob_end_clean(); }
@header('Content-Type: application/pdf');
@header('Content-Disposition: attachment; filename="'.$filename.'"');
$pdf->Output($filename, 'D');

// Debug zurücksetzen
$CFG->debugdisplay = $__prevdebugdisplay;
exit;