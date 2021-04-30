# IBAN Validator for Caldera Forms

This plugin adds a new processor for Caldera Forms, which validates a field for a syntactically correct IBAN after submitting the form.

**Please note:** This processor only validates the IBANs checksum, it can't check, if the IBAN really exists.

# Installation

* Download the [ZIP](https://github.com/moewe-io/caldera-forms-iban-validator/archive/refs/heads/master.zip)
* Install it manually at "Plugins" -> "Add new" -> "Upload plugin" 
* Activate the plugin

# Usage

* Create a field for the IBAN
* Add a new "IBAN validator" processor to the form
  * Insert the IBAN fields slug at the processor configuration
  
-> Now, when a user submits the form, the IBAN should be checked.
