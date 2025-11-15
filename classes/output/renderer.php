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

namespace auth_email_moddaker\output;

use moodleform;
use context_course;

/**
 * Renderer for Email-based self-registration Moddaker
 *
 * @package    auth_email_moddaker
 * @copyright  2025 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render the login signup form into a nice template for the theme.
     *
     * @param moodleform $form
     * @return string
     */
    public function render_moddaker_signup_form($form) {
        global $SITE;

        $context = $form->export_for_template($this);
        $url     = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context['logourl']  = $url;
        $context['sitename'] = format_string(
            $SITE->fullname,
            true,
            [ 'context' => context_course::instance(SITEID), "escape" => false ]
        );

        return $this->render_from_template('auth_email_moddaker/signup_form_layout', $context);
    }
}
