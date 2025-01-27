<?php

namespace Arminfrey\ContaoGbmBundle\Cron;

use Arminfrey\ContaoGbmBundle\SendMail\SendMail;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;

#[AsCronJob('daily')]
class SendMailCron
{
    /*public function __construct(
        private readonly SendMail $SendMail,
    ) {
    }*/

    public function __invoke(): void
    {
        $this->SendMail->sendBirthdayMail();
    }
}
