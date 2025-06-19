<?php

namespace Arminfrey\ContaoGbmBundle\Cron;

use Arminfrey\ContaoGbmBundle\SendMail\SendMail;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;

#[AsCronJob('0 6 * * *')]
class SendMailCron
{
    public function __construct(
        private readonly SendMail $sendMail
    ) {
    }

    public function __invoke(): void
    {
        $this->sendMail->sendBirthdayMail();
    }
}
