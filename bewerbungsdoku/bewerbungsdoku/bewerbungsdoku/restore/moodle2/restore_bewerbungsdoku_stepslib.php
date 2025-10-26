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

class restore_bewerbungsdoku_activity_structure_step extends restore_activity_structure_step {
    protected function define_structure() {
        return [ new restore_path_element('bewerbungsdoku', '/activity/bewerbungsdoku') ];
    }
    public function process_bewerbungsdoku($data) {
        global $DB;
        $data = (object)$data;
        $data->course = $this->get_courseid();
        $newitemid = $DB->insert_record('bewerbungsdoku', $data);
        $this->apply_activity_instance($newitemid);
    }
    protected function after_execute() {
        $this->add_related_files('mod_bewerbungsdoku', 'intro', null);
    }
}
