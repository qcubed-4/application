# About `i18n` directory

This folder contains any custom language files for i18n translations.  Language
files must be in the `.po` format (please see
    `http://www.gnu.org/software/gettext/manual/html_node/gettext_9.html`
for more information).

Note that QCubed will ignore some parts of the .po file format, including any
delimiters in the comments (e.g. references, flags, fuzzy, etc.)

For examples or a starting point of these files, please refer to the distributed
language files in `/vendor/qcubed-4/application/i18n/`.

The QI18n translator will process files in both this directory as well as 
language files in the QCubed core.  Moreover, the translator will take into
account the country code wherever applicable.

For example, if we are running with a Language Code of "en" and a Country Code
of "us", then the PoParser will process the following files in this order (where
subsequent files will override any defined tokens in previous ones):
* `/vendor/qcubed-4/application/i18n/en.po`
* `/vendor/qcubed-4/application/i18n/en_us.po` (will override anything so far processed)
* `/project/i18n/en.po` (will override anything so far processed)
* `/project/i18n/en_us.po` (will override anything so far processed)

(If any of the files in that list does not exist, it will simply be ignored.)

Another example: if we are running with just the Language Code of "es" and with
no specified country code, then PoParser will process these files in the
following order:
* `/vendor/qcubed-4/application/i18n/es.po`
* `/project/i18n/es.po` (will override anything so far processed)
