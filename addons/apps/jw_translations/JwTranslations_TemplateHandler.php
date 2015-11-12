<?php

require 'JwTranslations_Util.php';

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
        $TranslationHelper = JwTranslations_Util::fetch();

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
        $Tag = new PerchXMLTag($opening_tag);

        return [
            'key'   => $Tag->id(),
            'value' => $Tag->default()
        ];
    }
}
