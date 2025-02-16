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

/**
 * Callback implementations for Email-based self-registration Moddaker
 *
 * @package    auth_email_moddaker
 * @copyright  2025 Wail Abualela wailabualela@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Inject validation into signup_form.
 * @param array $data the data array from submitted form values.
 * @return array $errors the updated array of errors from validation.
 */
function auth_email_moddaker_validate_extend_signup_form($data) {
    $pluginsfunction = get_plugins_with_function('validate_extend_signup_form');
    $errors          = [];
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginerrors = $pluginfunction($data);
            $errors       = array_merge($errors, $pluginerrors);
        }
    }
    return $errors;
}

function auth_email_moddaker_fields_by_shortnames(MoodleQuickForm $mform, array $shortnames = []) : void {

    if ($fields = profile_get_signup_fields()) {
        foreach ($fields as $field) {
            if (! in_array($field->object->field->shortname, $shortnames)) {
                continue;
            }
            $field->object->field->defaultdata = $mform->_defaultValues['profile_field_' . $field->object->field->shortname] ?? null;

            $field->object->edit_field($mform);
        }
    }
}

/**
 * Validates the standard sign-up data (except recaptcha that is validated by the form element).
 *
 * @param  array $data  the sign-up data
 * @param  array $files files among the data
 * @return array list of errors, being the key the data element name and the value the error itself
 * @since Moodle 3.2
 */
function auth_email_moddaker_signup_validate_data($data, $files) {
    global $CFG, $DB;

    $errors = [];
    $authplugin = get_auth_plugin($CFG->registerauth);

    if (! validate_email($data['email'])) {
        $errors['email'] = get_string('invalidemail');

    } else if (empty($CFG->allowaccountssameemail)) {
        // Emails in Moodle as case-insensitive and accents-sensitive. Such a combination can lead to very slow queries
        // on some DBs such as MySQL. So we first get the list of candidate users in a subselect via more effective
        // accent-insensitive query that can make use of the index and only then we search within that limited subset.
        $sql = "SELECT 'x'
                  FROM {user}
                 WHERE " . $DB->sql_equal('email', ':email1', false, true) . "
                   AND id IN (SELECT id
                                FROM {user}
                               WHERE " . $DB->sql_equal('email', ':email2', false, false) . "
                                 AND mnethostid = :mnethostid)";

        $params = array(
            'email1' => $data['email'],
            'email2' => $data['email'],
            'mnethostid' => $CFG->mnet_localhost_id,
        );

        // If there are other user(s) that already have the same email, show an error.
        if ($DB->record_exists_sql($sql, $params)) {
            $forgotpasswordurl = new moodle_url('/login/forgot_password.php');
            $forgotpasswordlink = html_writer::link($forgotpasswordurl, get_string('emailexistshintlink'));
            $errors['email'] = get_string('emailexists') . ' ' . get_string('emailexistssignuphint', 'moodle', $forgotpasswordlink);
        }
    }
    if (empty($data['email2'])) {
        $errors['email2'] = get_string('missingemail');

    } else if (core_text::strtolower($data['email2']) != core_text::strtolower($data['email'])) {
        $errors['email2'] = get_string('invalidemail');
    }
    if (!isset($errors['email'])) {
        if ($err = email_is_not_allowed($data['email'])) {
            $errors['email'] = $err;
        }
    }

    // Construct fake user object to check password policy against required information.
    $tempuser = new stdClass();
    // To prevent errors with check_password_policy(),
    // the temporary user and the guest must not share the same ID.
    $tempuser->id = (int)$CFG->siteguest + 1;
    $tempuser->firstname = $data['firstname'];
    $tempuser->lastname = $data['lastname'];
    $tempuser->email = $data['email'];

    $errmsg = '';
    if (!check_password_policy($data['password'], $errmsg, $tempuser)) {
        $errors['password'] = $errmsg;
    }

    // Validate customisable profile fields. (profile_validation expects an object as the parameter with userid set).
    $dataobject = (object)$data;
    $dataobject->id = 0;
    
    $errors += profile_validation($dataobject, $files);

    return $errors;
}

function auth_email_moddaker_extend_navigation(global_navigation $nav) {
    global $PAGE;

    $PAGE->requires->css(("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/css/intlTelInput.css"));
    $PAGE->requires->js(("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/intlTelInput.min.js"));
    $PAGE->requires->js("$CFG->dirroot/auth/email_moddaker/js/phone_validation.js", true);
    
}