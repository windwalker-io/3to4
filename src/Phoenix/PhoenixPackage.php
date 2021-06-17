<?php
/**
 * Part of Phoenix project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Phoenix;

use Phoenix\Listener\JsCommandListener;
use Windwalker\Legacy\Core\Language\Translator;
use Windwalker\Legacy\Core\Package\AbstractPackage;

define('PHOENIX_ROOT', dirname(__DIR__));
define('PHOENIX_SOURCE', PHOENIX_ROOT . '/src');
define('PHOENIX_TEMPLATES', PHOENIX_ROOT . '/templates');

/**
 * The SimpleRADPackage class.
 *
 * @since  1.0
 * @deprecated Legacy code
 */
class PhoenixPackage extends AbstractPackage
{
    /**
     * init
     *
     * @return  void
     * @throws \ReflectionException
     * @throws \Windwalker\Legacy\DI\Exception\DependencyResolutionException
     */
    public function boot()
    {
        parent::boot();

        Translator::loadFile('phoenix', 'ini', $this);

        if ($this->app->isWeb()) {
            $this->getDispatcher()->addListener(new JsCommandListener());
        }
    }
}
