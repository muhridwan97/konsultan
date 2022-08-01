<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_ext_field')) {
    /**
     * Helper get single value of settings.
     * @param $field_name
     * @param $booking_id_field
     * @param string $field_alias
     * @return string
     */
    function get_ext_field($field_name, $booking_id_field, $field_alias = '')
    {
        $field_alias = empty($field_alias) ? $field_name : $field_alias;
        return "(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = {$booking_id_field}
              AND ref_extension_fields.field_name = '{$field_name}'
              LIMIT 1) AS {$field_alias}";
    }
}