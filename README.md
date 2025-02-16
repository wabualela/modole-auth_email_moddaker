```markdown
# Email Authentication Plugin for Moodle

This plugin provides email-based authentication for Moodle, allowing users to sign in using their email addresses instead of traditional usernames.

## Installation

1. Download the plugin files to the `/auth/email/` directory in your Moodle installation.
2. Navigate to Site Administration > Notifications to complete the installation.
3. Enable the plugin in Site Administration > Plugins > Authentication > Manage Authentication.

## Configuration

* **Email Domain Restriction:** Set allowed email domains
* **Verification Period:** Set the expiry time for verification links
* **Email Templates:** Customize verification email content

## Settings

Access plugin settings in Site Administration > Plugins > Authentication > Email Authentication:

* Enable/disable self-registration
* Configure password policy
* Set email verification requirements
* Customize welcome messages

## Developer API

### Available Hooks

* `auth_email_user_authenticated`: Triggered after successful authentication
* `auth_email_user_created`: Fired when a new user account is created
* `auth_email_verify_request`: Called when verification email is sent

## Requirements

* Moodle 3.9 or higher
* PHP 7.3 or higher
* Configured SMTP server

## Version History

* **1.0.0**
    * Initial release
    * Basic email authentication
    * User registration system

## Support

For support:
1. Check Moodle documentation
2. Visit Moodle forums
3. Report issues on GitHub

## License

GPL v3 or later
```