### About

Cloudflare extension for phpBB.

[![Build Status](https://img.shields.io/github/actions/workflow/status/AlfredoRamos/phpbb-ext-cloudflare/ci.yml?style=flat-square)](https://github.com/AlfredoRamos/phpbb-ext-cloudflare/actions)
[![Latest Stable Version](https://img.shields.io/github/tag/AlfredoRamos/phpbb-ext-cloudflare.svg?label=stable&style=flat-square)](https://github.com/AlfredoRamos/phpbb-ext-cloudflare/releases)
[![Code Quality](https://img.shields.io/codacy/grade/880e827356774dcf8348b803ff5b6855.svg?style=flat-square)](https://app.codacy.com/gh/AlfredoRamos/phpbb-ext-cloudflare/dashboard)
[![Translation Progress](https://badges.crowdin.net/phpbb-ext-cloudflare/localized.svg)](https://crowdin.com/project/phpbb-ext-cloudflare)
[![License](https://img.shields.io/github/license/AlfredoRamos/phpbb-ext-cloudflare.svg?style=flat-square)](https://raw.githubusercontent.com/AlfredoRamos/phpbb-ext-cloudflare/main/license.txt)

Adds [Cloudflare Turnstile](https://www.cloudflare.com/application-services/products/turnstile/) as a new CAPTCHA plugin for the **Spambot countermeasures** in the Administration Control Panel.

Turnstile displays a checkbox similar to reCAPTCHA, but offers enhanced privacy, rewards for challenges, works in countries where Google reCAPTCHA is blocked and it allows website administrator to change the CAPTCHA difficulty through Turnstile website.

### Features

- Protects user privacy
- It allows to change the widget theme and size
- It works on countries where reCAPTCHA is blocked
- You can change the difficulty of the challenges by site
- It's compatible with other extensions that displays CAPTCHAs such as **Contact Admin**

### Preview

See the [full blog post](https://alfredoramos.mx/cloudflare-extension-for-phpbb/) for the screenshots gallery.

### Requirements

- PHP 8.1.0 or greater
- phpBB 3.3 or greater
- Turnstile site key and account secret key

### Support

- [**Download page**](https://github.com/AlfredoRamos/phpbb-ext-cloudflare/releases)
- [GitHub issues](https://github.com/AlfredoRamos/phpbb-ext-cloudflare/issues)
- [Crowdin translations](https://crowdin.com/project/phpbb-ext-cloudflare)

### Donate

If you like or found my work useful and want to show some appreciation, you can consider supporting its development by [**giving a donation**](https://alfredoramos.mx/donate/).

### Installation

- Download the [latest release](https://github.com/AlfredoRamos/phpbb-ext-cloudflare/releases)
- Decompress the `*.zip` or `*.tar.gz` file
- Copy the files and directories inside `{PHPBB_ROOT}/ext/alfredoramos/cloudflare/`
- Go to your `Administration Control Panel` > `Customize` > `Manage extensions`
- Click on `Enable` and confirm

### Turnstile

In order to use Turnstile on your phpBB board, you need to generate a site key and copy your secret key.

To do so, go to [Cloudflare Turnstile](https://dash.cloudflare.com/?to=/:account/turnstile) and sign up if you don't have an account already.

Once logged in, go to [Turnstile dashboard](https://dash.cloudflare.com/?to=/:account/turnstile) and generate a new widget on the `Add widget` button.

Add a descriptive name, the host for your board and select the `Managed` widget mode.

Further down, you can optionally opt-in for the pre-clearance cookies. If in doubt, choose `No`.

Now, **copy the site key and secret key** shown.

### Configuration

- Login to your phpBB `Administration Control Panel`
- Go to `General` > `Board configuration` > `Spambot countermeasures`
- Go to the section `Available plugins` and choose `Turnstile` in the `Installed plugins` menu
- Click on the `Configure` button
- Paste the site key in the `Site key` field
- Paste the account secret key in the `Secret key` field
- Optionally choose a theme and size of the generated widget
- Click on `Submit` to save the configuration changes

### Uninstallation

- Go to your `Administration Control Panel` > `Customize` > `Manage extensions`
- Click on `Disable` and confirm
- Go back to `Manage extensions` > `Cloudflare Turnstile` > `Delete data` and confirm

### Upgrade

- Go to your `Administration Control Panel` > `Customize` > `Manage extensions`
- Click on `Disable` and confirm
- Delete all the files inside `{PHPBB_ROOT}/ext/alfredoramos/cloudflare/`
- Download the new version
- Upload the new files inside `{PHPBB_ROOT}/ext/alfredoramos/cloudflare/`
- Enable the extension again
