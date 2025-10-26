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







function xmldb_bewerbungsdoku_upgrade($oldversion) {



    global $DB;



    $dbman = $DB->get_manager();







    // Add participantemail field.



    if ($oldversion < 2025100906) {



        $table = new xmldb_table('bewerbungsdoku_entries');



        $field = new xmldb_field('participantemail', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'title');



        if (!$dbman->field_exists($table, $field)) {



            $dbman->add_field($table, $field);



        }



        upgrade_mod_savepoint(true, 2025100906, 'bewerbungsdoku');



    }







    return true;



}



