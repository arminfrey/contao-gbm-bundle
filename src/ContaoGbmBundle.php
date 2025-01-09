<?php
// src/ContaoGbmBundle.php
namespace Arminfrey\ContaoGbmBundle;

use App\DependencyInjection\ContaoGbmExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ContaoGbmBundle extends AbstractBundle
{
  public function getContainerExtension(): ?ExtensionInterface
    {
        return new ContaoGbmExtension();
    }
}
