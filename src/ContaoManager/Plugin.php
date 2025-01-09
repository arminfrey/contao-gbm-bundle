<?php
// src/ContaoManager/Plugin.php
namespace Arminfrey\ContaoGbmBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\CoreBundle\ContaoCoreBundle;
use Arminfrey\ContaoGbmBundle\ContaoGbmBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoGbmBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
