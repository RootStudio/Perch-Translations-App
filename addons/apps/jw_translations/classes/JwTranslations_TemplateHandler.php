<?php

if(!defined('PERCH_TRANSLATION_LANG')) {
    define('PERCH_TRANSLATION_LANG', 'en');
}

/**
 * Class JwTranslations_TemplateHandler
 * @author James Wigger <james@rootstudio.co.uk>
 */
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
            $replacement_tags = array();

            if($count > 0) {

                // Increment unique ID on tags
                $increment = 1;
                foreach($matches as $match) {
                    $output = $this->parse_tags($match[0]);
                    $replacement_tags[$output['key'] . '#' . $increment] = $output['value'];

                    $increment++;
                }

                // Rewrite template IDs for unique strings
                $html = preg_replace_callback($s, function($matches) {
                    foreach($matches as $match) {
                        static $counter = 0;
                        $counter++;

                        $Tag = new PerchXMLTag($match);
                        return str_replace($Tag->id(), $Tag->id() . '#' . $counter, $match);
                    }
                }, $html);
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
    private function parse_tags($opening_tag)
    {
        $TranslationHelper = JwTranslations_Loader::fetch();
        $Tag = new PerchXMLTag($opening_tag);

        $translation_key = $Tag->id();
        $translation_lang = strtolower($Tag->lang() ? $Tag->lang() : PERCH_TRANSLATION_LANG);
        $translation_default_message = $Tag->default() ? $Tag->default() : null;

        $value_string = $TranslationHelper->get_translation($translation_key, $translation_lang, $translation_default_message);

        return array(
            'key'   => $translation_key,
            'value' => $this->parse_placeholders($value_string, $Tag)
        );
    }

    /**
     * Parses placeholder attributes from tag
     *
     * @param string $value_string
     * @param PerchXMLTag $Tag
     * @return string
     */
    private function parse_placeholders($value_string, PerchXMLTag $Tag)
    {
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

        return $value_string;
    }
}
