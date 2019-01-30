<?php
/*
 * Plugin Name: Caldera Forms IBAN Validator
 * Description: Validator for iban field.
 * Author: MOEWE
 * Author URI: https://www.moewe.io
 * Text Domain: caldera-forms-iban-validator
*/

add_filter('caldera_forms_get_form_processors', 'caldera_forms_iban_validator_processor');
function  caldera_forms_iban_validator_processor($processors)
{
    $processors['caldera_forms_iban_validator'] = array(
        'name' => __('IBAN Validator', 'caldera-forms-iban-validator'),
        'description' => __('Processor to validate IBAN field.'),
        'pre_processor' => 'caldera_forms_iban_validator_pre_processor',
        'template' => __DIR__ . '/templates/caldera_forms_iban_validator_processor.config.php'
    );
    return $processors;
}

function caldera_forms_iban_validator_pre_processor($config, $form)
{
    $cf_iban_slug = Caldera_Forms::do_magic_tags($config['cf_iban_slug']);

    foreach ($form['fields'] as $field) {
        if ($field['slug'] == $cf_iban_slug) {
            $cf_iban_field_id = $field['ID'];
            break;
        }
    }


    if (!$cf_iban_field_id) {
        return;
    }

    $raw_data = Caldera_Forms::get_submission_data($form);

    $iban_field_value = $raw_data[$cf_iban_field_id];

    if(!caldera_forms_iban_validator_check_iban($iban_field_value)) {
        return array(
            'note' => __('IBAN ungültig: Die Berechnung der Prüfziffer hat ergeben, dass die Eingabe keine gültige IBAN ist.','caldera-forms-iban-validator'),
            'type' => 'error'
        );
    }

    return;
}


function caldera_forms_iban_validator_check_iban($iban) {

    // Normalize input (remove spaces and make upcase)
    $iban = strtoupper(str_replace(' ', '', $iban));

    if (preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
        $country = substr($iban, 0, 2);
        $check = intval(substr($iban, 2, 2));
        $account = substr($iban, 4);

        // To numeric representation
        $search = range('A','Z');
        foreach (range(10,35) as $tmp)
            $replace[]=strval($tmp);
        $numstr=str_replace($search, $replace, $account.$country.'00');

        // Calculate checksum
        $checksum = intval(substr($numstr, 0, 1));
        for ($pos = 1; $pos < strlen($numstr); $pos++) {
            $checksum *= 10;
            $checksum += intval(substr($numstr, $pos,1));
            $checksum %= 97;
        }

        return ((98-$checksum) == $check);
    } else
        return false;
}

function caldera_forms_iban_validator_fields()
{
    return array(
        array(
            'id' => 'cf_iban_slug',
            'label' => 'IBAN Slug',
            'type' => 'text',
            'required' => true,
            'magic' => false,
        ),
    );
}
