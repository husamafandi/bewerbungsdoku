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







$capabilities = [



    'mod/bewerbungsdoku:addinstance' => [



        'riskbitmask' => RISK_XSS,



        'captype' => 'write',



        'contextlevel' => CONTEXT_COURSE,



        'archetypes' => [



            'editingteacher' => CAP_ALLOW,



            'manager' => CAP_ALLOW,



        ],



        'clonepermissionsfrom' => 'moodle/course:manageactivities'



    ],







    'mod/bewerbungsdoku:view' => [



        'captype' => 'read',



        'contextlevel' => CONTEXT_MODULE,



        'archetypes' => [



            'student' => CAP_ALLOW,



            'teacher' => CAP_ALLOW,



            'editingteacher' => CAP_ALLOW,



            'manager' => CAP_ALLOW,



        ]



    ],







    'mod/bewerbungsdoku:submit' => [



        'captype' => 'write',



        'contextlevel' => CONTEXT_MODULE,



        'archetypes' => [



            'student' => CAP_ALLOW,



            'teacher' => CAP_ALLOW,



            'editingteacher' => CAP_ALLOW,



            'manager' => CAP_ALLOW,



        ]



    ],







    'mod/bewerbungsdoku:viewallentries' => [



        'captype' => 'read',



        'contextlevel' => CONTEXT_MODULE,



        'archetypes' => [



            'teacher' => CAP_ALLOW,



            'editingteacher' => CAP_ALLOW,



            'manager' => CAP_ALLOW,



        ]



    ],







    'mod/bewerbungsdoku:exportentries' => [



        'captype' => 'read',



        'contextlevel' => CONTEXT_MODULE,



        'archetypes' => [



            'teacher' => CAP_ALLOW,



            'editingteacher' => CAP_ALLOW,



            'manager' => CAP_ALLOW,



        ]



    ],



];



