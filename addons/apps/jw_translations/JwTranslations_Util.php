<?php require('vendor/autoload.php');

use Dflydev\DotAccessData\Data;

/**
 * Class JwTranslations_Util
 *
 * Translation utility class
 */
class JwTranslations_Util
{
    /**
     * Singleton instance
     *
     * @var JwTranslations_Util
     */
    static private $instance;

    /**
     * Translation data container
     *
     * @var Data
     */
    private $translations;

    /**
     * Singleton loader
     *
     * @return JwTranslations_Util
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
        PerchUtil::debug('Using translation: ' . $lang . '.' . $id);
        return $this->translations->get($lang . '.' . $id, $default);
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
        $base_path = PerchUtil::file_path(PERCH_PATH . '/translations/');
        $dir_iterator = new RecursiveDirectoryIterator($base_path, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);

        $files = [];

        foreach ($iterator as $fileinfo) {
            if($fileinfo->getExtension() == 'php') {

                $file_path = PerchUtil::file_path($fileinfo->getPathname());

                if($fileinfo->isDir()) {
                    $path = [$fileinfo->getFilename() => []];
                }
                else {
                    $path = [trim($fileinfo->getFilename(), '.php') => $this->load_translation_data($file_path)];
                }

                for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
                    $path = [$iterator->getSubIterator($depth)->current()->getFilename() => $path];
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
            PerchUtil::debug('Loading translation file: ' . $path);
            return include $path;
        }
    }
}
