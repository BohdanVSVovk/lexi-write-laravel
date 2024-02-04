<?php


if (!function_exists('activeMenu')) {
    /**
     * menu activated
     *
     * @param string $routedName
     * @return mixed
     */
    function activeMenu(...$routeName )
    {
        if (in_array(url()->current(), $routeName)) {
            return ['color1' => '#E60C84', 'color2' => '#FFCF4B', 'class' => 'bg-color-F6 dark:bg-color-47 border-design-1', 'collapse' => 'show', 'parent-border' => 'border-design-1'];
        }

       return ['color1' => '#141414', 'color2' => '#141414', 'class' => '', 'collapse' => '', 'parent-border' => ''];
    }
}

if (!function_exists('accountSidebarActiveMenu')) {
    /**
     * menu activated
     *
     * @param string $routedName
     * @return mixed
     */
    function accountSidebarActiveMenu(...$routeName )
    {
        if (in_array(url()->current(), $routeName)) {
            return ['class' => 'border-design-3-active', 'color1' => '#E60C84', 'color2' => '#FFCF4B' ];
        }

       return ['class' => 'border-color-DF dark:border-[#474746]', 'color1' => '#898989', 'color2' => '#898989'];
    }
}
 
if (!function_exists('temperature')) {
    /**
     * content level
     *
     * @param string $temperature
     * @return mixed
     */
    function temperature($temperature)
    {
        $value = 0;

        switch($temperature) {

            case "Optimal" :
                $value = 0.5;
                break;
            case "Low" :
                $value = 0.8;
                break;
            case "Medium" :
                $value = 0.9;
                break;
            case "High" :
                $value = 1;
                break;
        }

        return $value;
    }
}

if (!function_exists('codeLabel')) {
    /**
     * Code label
     *
     * @param string $label
     * @return mixed
     */
    function codeLabel($label, $swap=false)
    {
        
        if ($swap) {
            $codeLabel = ['Easy' => 'Noob', 'Medium' => 'Moderate', 'High' => 'High'];
        } else {
            $codeLabel = ['Noob' => 'Easy', 'Moderate' => 'Medium', 'High' => 'High'];
        }

        return array_key_exists($label, $codeLabel) ? $codeLabel[$label] : '';  
    }

}

if (!function_exists('variant')) {
    /**
     * Variant
     *
     * @param string $variant
     * @return mixed
     */
    function variant($variant)
    {
        
        $variantLabel = [
            '1' => 'one', 
            '2' => 'two', 
            '3' => 'three'
        ];

        return array_key_exists($variant, $variantLabel) ? $variantLabel[$variant] : '';  
    }

}


if (!function_exists('processApiPreferenceData')) {
    /**
     * Process API Preference Data
     *
     * @param string $key
     * @param array $array
     * @return array
     */
    function processApiPreferenceData($key, $array)
    {
        $data = [];
        foreach ($array as $value) {
            switch($key) {
                case "codeLabel":
                    $data[] = [codeLabel($value) => $value];
                    break;
                case "temperature":
                    $data[] = [$value => temperature($value)];
                    break;
                case "variant":
                    $data[] = [variant($value) => $value];
                    break;
                default:
                    $data[] = [$value => $value];
                    break;
            }
        }

        return $data;
    }
}

if (!function_exists('creativityLabel')) {
    /**
     * Converts integer to text.
     *
     * Example output: 'High', 'Low', 'Medium'.
     *
     * @param string $bytes
     * @param string $unit
     * @return string
     */
    function creativityLabel($label)
    {
        $creativitylabel = [
            0.5 => "Optimal",
            0.8 => "Low",
            0.9 => "Medium",
            1 => "High"
        ];  
        return array_key_exists($label,$creativitylabel) ? $creativitylabel[$label] : null;  
    }
}

if (!function_exists('processPreferenceData')) {
    /**
     * Process Preference Data
     *
     * @param string $value
     * @return array
     */
    function processPreferenceData($value)
    {
        return $value != NULL ? json_decode($value, true) : []; 
    }
}

if (!function_exists('apiKey')) {
    /**
     * Get API Key
     *
     * @param string $value
     * @return [type]
     */
    function apiKey($value)
    {
        return preference($value) ?? '';
    }
}
