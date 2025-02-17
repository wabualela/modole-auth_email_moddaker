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
 *  Moddkaer signup form
 *
 * @package    auth_email_moddaker
 * @copyright  2025 Wail Abualela wailabualela@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once "$CFG->libdir/formslib.php";
require_once "$CFG->dirroot/user/profile/lib.php";
require_once "$CFG->dirroot/user/editlib.php";
require_once "$CFG->dirroot/auth/email_moddaker/lib.php";

class login_signup_form extends moodleform implements renderable, templatable {
    public function definition() {
        global $USER, $CFG;

        $mform = $this->_form;
        $mform->updateAttributes(['class' => 'row']);

        $namefields = useredit_get_required_name_fields();
        foreach ($namefields as $field) {
            $mform->addElement('text', $field, get_string($field), 'maxlength="100" class="col-md-6"');
            $mform->setType($field, core_user::get_property_type('firstname'));
            $stringid = 'missing' . $field;
            if (! get_string_manager()->string_exists($stringid, 'moodle')) {
                $stringid = 'required';
            }
            $mform->addRule($field, get_string($stringid), 'required', null, 'client');
        }

        $mform->addElement('text', 'profile_field_certfullname', get_string('certfullname', 'auth_email_moddaker'), 'maxlength="100" class="col-md-12"');
        $mform->addRule('profile_field_certfullname', get_string('missingcertfullname','auth_email_moddaker'), 'required', null, 'client');
        $mform->addRule('profile_field_certfullname', get_string('invalidcertfullname', 'auth_email_moddaker'), 'regex', '/^[\p{L}\s\'-]+$/u', 'client');
        $mform->setForceLtr('profile_field_certfullname');
        
        $mform->addElement('html', '<div class="col-md-4">');
        auth_email_moddaker_fields_by_shortnames($mform, ['gender']);
        $mform->addElement('html', '</div>');
        
        $options = array(
            'startyear' => 1950,
            'stopyear'  => 2015,
            'timezone'  => 99,
            'optional'  => false
        );
        $mform->addElement('html', '<div class="col-md-8">');
        $mform->addElement('date_selector', 'profile_field_dob', get_string('dob', 'auth_email_moddaker'), $options);
        $mform->addRule('profile_field_dob', get_string('missingdob', 'auth_email_moddaker'), 'required', null, 'client');        // $mform->addElement('html', '</div');
        $mform->addElement('html', '</div>');
    
        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" class="col-md-6"');
        $mform->setType('email', core_user::get_property_type('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email');

        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" class="col-md-6"');
        $mform->setType('email2', core_user::get_property_type('email'));
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email2');

        $mform->addElement('text', 'phone1', get_string('phonenumber', 'auth_email_moddaker'), 'maxlength="20" class="col-md-6"');
        $mform->setType('phone1', core_user::get_property_type('phone1'));
        $mform->addRule('phone1', get_string('missingphonenumber', 'auth_email_moddaker'), 'required', null, 'client');
        $mform->addRule('phone1', get_string('invalidphonenumber', 'auth_email_moddaker'), 'regex', '/^\+?[1-9]\d{9,14}$/', 'client');
        $mform->setForceLtr('phone1');    

        $mform->addElement('password', 'password', get_string('password'), [
            'maxlength' => MAX_PASSWORD_CHARACTERS,
            'class' => 'col-md-6',
            'autocomplete' => 'new-password'
        ]);
        $mform->setType('password', core_user::get_property_type('password'));
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');
        $mform->addRule('password', get_string('maximumchars', '', MAX_PASSWORD_CHARACTERS),
            'maxlength', MAX_PASSWORD_CHARACTERS, 'client');

        $country             = get_string_manager()->get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country             = array_merge($default_country, $country);
        $mform->addElement('select', 'profile_field_nationality', get_string('nationality', 'auth_email_moddaker'), $country, 'class="col-md-6"');
        $mform->addRule('profile_field_nationality', get_string('missingnationality', 'auth_email_moddaker'), 'required', null, 'client');
        $mform->setDefault('profile_field_nationality', ''); // Set empty default to force selection

        $country             = get_string_manager()->get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country             = array_merge($default_country, $country);
        $mform->addElement('select', 'country', get_string('country'), $country, 'class="col-md-6"');
        $mform->addRule('country', get_string('missingcountry', 'auth_email_moddaker'), 'required', null, 'client');

        if (! empty($CFG->country)) {
            $mform->setDefault('country', $CFG->country);
        } else {
            $mform->setDefault('country', '');
        }

       // Hook for plugins to extend form definition.
        core_login_extend_signup_form($mform);

        // Add "Agree to sitepolicy" controls. By default it is a link to the policy text and a checkbox but
        // it can be implemented differently in custom sitepolicy handlers.
        $manager = new \core_privacy\local\sitepolicy\manager();
        $manager->signup_form($mform);

        $manager = new \core_privacy\local\sitepolicy\manager();
        if ($manager->is_defined()) {
            $mform->addElement('checkbox', 'sitepolicyagree', '', '<a href="' . $manager->get_redirect_url() . '">' . get_string('policyagreementclick') . '</a>','class="col-md-12"');
            $mform->addRule('sitepolicyagree', get_string('required'), 'required', null, 'client');
        }
        $manager->signup_form($mform);

        // buttons
        $this->set_display_vertical();
        $this->add_action_buttons(true, get_string('createaccount'));

    }

    function definition_after_data() {
        $mform = $this->_form;
        $mform->applyFilter('email', 'trim');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }
    }

    /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Extend validation for any form extensions from plugins.
        // $errors = array_merge($errors, auth_email_moddaker_validate_extend_signup_form($data));

        if ($data['profile_field_gender'] == 0) {
            $errors['profile_field_gender'] = get_string('missinggender', 'auth_email_moddaker');
        }

         // Validate phone number (assuming it's stored in 'phone1' field)
        if (!preg_match('/^\+?[1-9]\d{9,14}$/', $data['phone1'])) {
            $errors['phone1'] = get_string('invalidphone', 'auth_email_moddaker');
        }

        if (signup_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (! empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (! $recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }
        }

        $errors += auth_email_moddaker_signup_validate_data($data, $files);

        return $errors;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        ob_start();
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        $context = [
            'formhtml' => $formhtml
        ];
        return $context;
    }
}
