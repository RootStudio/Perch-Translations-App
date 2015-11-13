<?php

require 'JwTranslations_Util.php';

if(!defined('PERCH_TRANSLATION_LANG')) {
    define('PERCH_TRANSLATION_LANG', 'en');
}

class JwTranslations_TemplateHandler extends PerchAPI_TemplateHandler
{
    /**
     * Tag identifier in templates
     *
     * @var string
     */
    public $tag_mask = 'trans';

    /**
     * Parse template contents and inject modifications
     *
     * @param $html
     * @param $Template
     * @return mixed
     */
    public function render_runtime($html, $Template)
    {
        if(strpos($html, 'perch:' . $this->tag_mask) !== false) {

            $s = '/<perch:'.$this->tag_mask.'\s[^>]*\/>/';
            $count = preg_match_all($s, $html, $matches, PREG_SET_ORDER);
            $replacement_tags = [];

            if($count > 0) {
                foreach($matches as $match) {
                    $output = $this->parse_tags($match[0]);
                    $replacement_tags[$output['key']] = $output['value'];
                }
            }

            $html = $Template->replace_content_tags($this->tag_mask, $replacement_tags, $html);
        }

        return $html;
    }

    /**
     * Parses perch:trans tag structure for attributes
     *
     * @param $opening_tag
     * @return array
     */
    public function parse_tags($opening_tag)
    {
        $TranslationHelper = JwTranslations_Util::fetch();
        $Tag = new PerchXMLTag($opening_tag);

        $translation_key = $Tag->id();
        $translation_lang = strtolower($Tag->lang() ? $Tag->lang() : PERCH_TRANSLATION_LANG);
        $translation_default_message = $Tag->default() ? $Tag->default() : null;

        $value_string = $TranslationHelper->get_translation($translation_key, $translation_lang, $translation_default_message);

        $s = '/:\w+/';
        $count = preg_match_all($s, $value_string, $matches, PREG_SET_ORDER);

        if($count > 0) {
            foreach($matches as $match) {
                $replacement = $match[0];
                $placeholder = str_replace('-', '_', trim($match[0], ':'));

                if($Tag->is_set("placeholder_{$placeholder}"))
                {
                    $value_string = str_replace($replacement, $Tag->{"placeholder_{$placeholder}"}, $value_string);
                }
            }
        }

        return [
            'key'   => $translation_key,
            'value' => $value_string
        ];
    }
}
