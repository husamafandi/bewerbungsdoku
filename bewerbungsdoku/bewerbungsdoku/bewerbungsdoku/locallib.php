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











/**



 * Render a simple A–Z initials bar similar to Moodle core.



 *



 * @param moodle_url $baseurl  Base url the letters should point to.



 * @param string $paramname    Query param that carries the selected letter.



 * @param string $current      Currently selected letter ('' means "Alle").



 * @return string HTML



 */



function mod_bewerbungsdoku_render_initials_bar(moodle_url $baseurl, string $paramname, string $current = ''): string {



    $letters = range('A', 'Z');







    // Clone base url to avoid side effects.



    $allurl = clone($baseurl);



    $allurl->params([$paramname => '']);



    $parts = [];



    $parts[] = html_writer::span(html_writer::link($allurl, get_string('all')), 'initialbar-all'.($current === '' ? ' active' : ''));



    foreach ($letters as $L) {



        $u = clone($baseurl);



        $u->params([$paramname => $L]);



        $class = 'initialbar-letter'.($current === $L ? ' active' : '');



        $parts[] = html_writer::span(html_writer::link($u, $L), $class);



    }



    return html_writer::div(implode(' ', $parts), 'initialbar d-flex gap-2 mt-2 mb-3');



}



