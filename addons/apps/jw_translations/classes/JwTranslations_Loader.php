<?php

require(PERCH_PATH . '/addons/apps/jw_translations/lib/Dflydev/DotAccessData/Util.php');
require(PERCH_PATH . '/addons/apps/jw_translations/lib/Dflydev/DotAccessData/DataInterface.php');
require(PERCH_PATH . '/addons/apps/jw_translations/lib/Dflydev/DotAccessData/Data.php');

use Dflydev\DotAccessData\Data;

/**
 * Class JwTranslations_Loader
 *
 * Translation utility class
 *
 * @author James Wigger <james@rootstudio.co.uk>
 */
class JwTranslations_Loader
{
    /**
     * Singleton instance
     *
     * @var JwTranslations_Loader
     */
    static private $instance;

    /**
     * Translation data container
     *
     * @var Data
     */
    private $translations;

    /**
     * Translation directory
     *
     * @var string
     */
    private $translation_dir = 'translations';

    /**
     * Singleton loader
     *
     * @return JwTranslations_Loader
     */
    public static function fetch()
    {
        if(!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    /**
     * Return a translation string from files using dot notation
     *
     * @param $id
     * @param string $lang
     * @param null $default
     * @return array|mixed|null
     */
    public function get_translation($id, $lang = 'en', $default = null)
    {
        PerchUtil::debug('Using translation: ' . $lang . '.' . $id, 'success');
        return $this->translations->get($lang . '.' . $id, $default ? $default : $id);
    }

    /**
     * JwTranslations_Util constructor.
     *
     * Loads files into memory to avoid disk reads
     */
    private function __construct()
    {
        $this->translations = $this->load_translation_files();
    }

    /**
     * Load files from translation directory and converts to dot notation
     *
     * @return Data
     */
    private function load_translation_files()
    {
        $base_path = PerchUtil::file_path(PERCH_PATH . '/' . $this->translation_dir);
        $dir_iterator = new RecursiveDirectoryIterator($base_path, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);

        $files = array();

        foreach ($iterator as $fileinfo) {
            if($fileinfo->getExtension() == 'php') {

                $file_path = PerchUtil::file_path($fileinfo->getPathname());

                if($fileinfo->isDir()) {
                    $path = array($fileinfo->getFilename() => array());
                }
                else {
                    $path = array(PerchUtil::strip_file_extension($fileinfo->getFilename()) => $this->load_translation_data($file_path));
                }

                for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
                    $path = array($iterator->getSubIterator($depth)->current()->getFilename() => $path);
                }

                $files = array_merge_recursive($files, $path);
            }
        }

        return new Data($files);;
    }

    /**
     * Import translation PHP file
     *
     * @param $path
     * @return mixed
     */
    private function load_translation_data($path)
    {
        if(file_exists($path)) {
            PerchUtil::debug('Loading translation file: ' . $path, 'template');
            return include $path;
        }
    }
}
