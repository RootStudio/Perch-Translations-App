<?php

$this->register_app('jw_translations', 'Translations', 5, 'Inject translation strings into templates', 1.0, true);

spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'JwTranslations') === 0) {
        include(PERCH_PATH . '/addons/apps/jw_translations/classes/' . $class_name . '.php');

        return true;
    }

    return false;
});
