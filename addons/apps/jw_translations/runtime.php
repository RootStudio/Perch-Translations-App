<?php

include('classes/JwTranslations_TemplateHandler.php');
include('classes/JwTranslations_Loader.php');

PerchSystem::register_template_handler('JwTranslations_TemplateHandler');

/**
 * Get translation string
 *
 * @param $id
 * @param array $opts
 * @param bool|false $return
 * @return array|mixed|null
 */
function get_translation($id, array $opts = array(), $return = false)
{
    $defaults = array(
        'default'      => null,
        'lang'         => PERCH_TRANSLATION_LANG,
        'placeholders' => false
    );

    $opts = array_merge($defaults, $opts);
    $TranslationHelper = JwTranslations_Loader::fetch();

    $value_string = $TranslationHelper->get_translation($id, $opts['lang'], $opts['default']);

    if($opts['placeholders']) {
        $s = '/:\w+/';
        $count = preg_match_all($s, $value_string, $matches, PREG_SET_ORDER);

        if($count > 0) {
            foreach ($matches as $match) {
                $replacement = $match[0];
                $placeholder = str_replace('-', '_', trim($match[0], ':'));

                if(array_key_exists($placeholder, $opts['placeholders'])) {
                    $value_string = str_replace($replacement, $opts['placeholders'][$placeholder], $value_string);
                }
            }
        }
    }

    if($return) return $value_string;
    echo $value_string;
}
