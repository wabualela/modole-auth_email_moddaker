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

namespace auth_email_moddaker;

require_once("$CFG->libdir/formslib.php");
/**
 * Class form
 *
 * @package    auth_email_moddaker
 * @copyright  2025 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class form extends \moodleform {
    public function definition() {
        global $CFG;
        \MoodleQuickForm::registerElementType(
            'phone',
            "$CFG->dirroot/auth/email_moddaker/classes/form/phone.php",
            'auth_email_moddaker\form\phone'
        );
        $mform = $this->_form; // Don't forget the underscore! 
        $mform->addElement('phone', 'name', 'Test Element');
    }
}
