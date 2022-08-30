<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_cardbox\output;

/*
 * @package   mod_cardbox
 * @copyright 2021 ITCenter RWTH Aachen (see README.md)
 * @author    Amrita Deb
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class previewtable extends \html_table {
    /** @var \csv_import_reader  */
    protected $cir;
    /** @var array */
    protected $filecolumns;
    /** @var int */
    protected $previewrows;
    /** @var bool */
    protected $noerror = true; // Keep status of any error.

    /**
     * preview constructor.
     *
     * @param \csv_import_reader $cir
     * @param array $filecolumns
     * @param int $previewrows
     * @throws \coding_exception
     */
    public function __construct(\csv_import_reader $cir, array $filecolumns) {
        parent::__construct();
        $this->cir = $cir;
        $this->filecolumns = $filecolumns;

        $this->id = "cbxpreview";
        $this->attributes['class'] = 'generaltable';
        $this->head = array();
        $this->data = $this->read_data($filecolumns);
        $this->head[] = get_string('uucsvline', 'tool_uploaduser');
        foreach ($filecolumns as $column) {
            $this->head[] = $column;
        }
        $this->head[] = ucfirst(get_string('status', 'cardbox'));
    }

    protected function read_data(array $filecolumns) {
        $this->cir->init();
        $i = 1; // Always start from 1 since 0 is csv column header.
        while ($fields = $this->cir->next()) {
            $errors = array();
            $atleastoneanswer = 0;
            $status = "";
            $rowcols = array();
            $rowcols['line'] = $i;
            foreach ($fields as $key => $field) {
                $rowcols[$this->filecolumns[$key]] = s(trim($field));
            }
            $errors = cardbox_import_validate_row($atleastoneanswer, $rowcols);
            $columnexceptions = cardbox_import_validate_columns($filecolumns, SHORT_DESCRIPTION);
            if (!empty($errors)) {
                $errorlines[] = $i;
                $status = "ERR:";
                foreach ($errors as $error) {
                    $status .= $error;
                }
            } else if (!empty($columnexceptions[0])) {
                $errorlines[] = $i;
                $status = "ERR:";
                foreach ($columnexceptions[0] as $error) {
                    $status .= $error;
                }
            } else {
                $status = "OK";
            }
            $rowcols[get_string('status', 'cardbox')] = $status;

            $data[] = $rowcols;
            $i++;
        }
        if ($fields = $this->cir->next()) {
            $data[] = array_fill(0, count($fields) + 2, '...');
        }
        $this->cir->close();
        return $data;
    }
}
