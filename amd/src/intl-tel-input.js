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
 * TODO describe module intl-tel-input
 *
 * @module     auth_email_moddaker/intl-tel-input
 * @copyright  2025 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import intlTelInput from "https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.4/build/js/intlTelInput.js";
import ar from "https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.4/build/js/i18n/ar";

define([], function () {
    return {
        init: function (selector) {
            const input = document.querySelector(selector);

            intlTelInput(input, {
                i18n: ar,
                initialCountry: "auto",
                loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@21.2.4/build/js/utils.js")
            });
        }
    };
});