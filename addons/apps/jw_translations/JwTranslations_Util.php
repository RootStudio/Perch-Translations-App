<?php

class JwTranslations_Util
{
    static private $instance;

    private $translations = [];

    public static function fetch()
    {
        if(!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    private function __construct()
    {
        var_dump($this->load_translation_files());
    }

    private function load_translation_files()
    {
        $base_path = PERCH_PATH . '/translations/';
        $path = PerchUtil::file_path($base_path . '**/*.php');
//        $files = glob($path);


        $dir_iterator = new RecursiveDirectoryIterator($base_path);
        $iterator = new RecursiveIteratorIterator($dir_iterator);
        $files = new RegexIterator($iterator, '/^.*\.(php)$/i', RegexIterator::GET_MATCH);

        $files_array = [];

        foreach($files as $file) {
            array_merge($files_array, $file);
        }

        var_dump($files_array);

//        $files = array_map(function($path) use($base_path) {
//            return str_replace($base_path, '', $path);
//        }, $files);

        return $files;
    }

    private function map_filesystem_to_array($path)
    {
        $files = PerchUtil::get_dir_contents($path);

        if(is_array($files)) {
            foreach($files as $file) {

            }
        }
    }
}