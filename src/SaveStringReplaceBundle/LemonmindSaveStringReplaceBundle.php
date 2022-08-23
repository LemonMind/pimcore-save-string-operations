<?php

declare(strict_types=1);

namespace Lemonmind\SaveStringReplaceBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class LemonmindSaveStringReplaceBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/lemonmindsavestringreplace/js/pimcore/element/helpers/gridColumnConfigExtended.js',
            '/bundles/lemonmindsavestringreplace/js/pimcore/makeWindow.js',
            '/bundles/lemonmindsavestringreplace/js/pimcore/concatWindow.js',
            '/bundles/lemonmindsavestringreplace/js/pimcore/startup.js',
        ];
    }
}
