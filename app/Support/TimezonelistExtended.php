<?php

namespace App\Support;

use DateTime;
use DateTimeZone;
use Jackiedo\Timezonelist\Timezonelist;

class TimezonelistExtended extends Timezonelist
{
    /**
     * Format to display timezones
     *
     * @param  string $timezone
     * @param  string $continent
     * @param bool $htmlencode
     * @return string
     */
    protected function formatTimezone($timezone, $continent, $htmlencode=true)
    {
        $time = new DateTime(null, new DateTimeZone($timezone));
        $offset = $time->format('P');
        if ($htmlencode) {
            $offset = str_replace('-', ' &minus; ', $offset);
            $offset = str_replace('+', ' &plus; ', $offset);
        }
        $timezone = substr($timezone, strlen($continent) + 1);
        $timezone = str_replace('St_', 'St. ', $timezone);
        $timezone = str_replace('_', ' ', $timezone);
        $formatted = '(GMT/UTC' . $offset . ')' . self::WHITESPACE_SEP . $timezone;
        return $formatted;
    }

    /**
     * Create a GMT timezone select element for form
     *
     * @param string $name
     * @param string $selected
     * @param mixed $attr
     * @param bool $htmlencode
     * @return string
     */
    public function create($name, $selected='', $attr='', $htmlencode=true)
    {
        // Attributes for select element
        $attrSet = '';
        if (!empty($attr)) {
            if (is_array($attr)) {
                foreach ($attr as $attr_name => $attr_value) {
                    $attrSet .= ' ' .$attr_name. '="' .$attr_value. '"';
                }
            } else {
                $attrSet = ' ' .$attr;
            }
        }
        // start select element
        $listbox = '<select name="' .$name. '"' .$attrSet. '>';
        // Add popular timezones
        $listbox .= '<optgroup label="General">';
        foreach ($this->popularTimezones as $key => $value) {
            $selected_attr = ($selected == $key) ? ' selected="selected"' : '';
            $listbox .= '<option value="' .$key. '"' .$selected_attr. '>' .$value. '</option>';
        }
        $listbox .= '</optgroup>';
        // Add all timezone of continents
        foreach ($this->continents as $continent => $mask) {
            $timezones = DateTimeZone::listIdentifiers($mask);
            // start optgroup tag
            $listbox .= '<optgroup label="' .$continent. '">';
            // create option tags
            foreach ($timezones as $timezone) {
                $selected_attr = ($selected == $timezone) ? ' selected="selected"' : '';
                $listbox .= '<option value="' .$timezone. '"' .$selected_attr. '>';
                $listbox .= $this->formatTimezone($timezone, $continent, $htmlencode);
                $listbox .= '</option>';
            }
            // end optgroup tag
            $listbox .= '</optgroup>';
        }
        // end select element
        $listbox .= '</select>';
        return $listbox;
    }

    /**
     * Create a timezone array
     *
     * @param bool $htmlencode
     * @return mixed
     */
    public function toArray($htmlencode=false)
    {
        $list = [];
        // Add popular timezones to list
        foreach ($this->popularTimezones as $key => $value) {
            $list['General'][$key] = $value;
        }
        // Add all timezone of continents to list
        foreach ($this->continents as $continent => $mask) {
            $timezones = DateTimeZone::listIdentifiers($mask);
            foreach ($timezones as $timezone) {
                $list[$continent][$timezone] = $this->formatTimezone($timezone, $continent, $htmlencode);
            }
        }
        return $list;
    }
}