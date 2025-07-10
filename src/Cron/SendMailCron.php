<?php

namespace Arminfrey\ContaoGbmBundle\Cron;

use Arminfrey\ContaoGbmBundle\SendMail\SendMail;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

#[AsCronJob('8 6 * * *')]
class SendMailCron
{
    public function __construct(
        private readonly SendMail $sendMail
    ) {
    }

    public function __invoke(): void
    {
        $store = new FlockStore(); // Oder z.B. PDOStore
        $factory = new LockFactory($store);
        $lock = $factory->createLock('send_birthday_mail_lock', 300); // 5 Minuten
        
        if (!$lock->acquire()) {
            // Bereits ein anderer Prozess läuft
            return;
        }
        
        try {
            $this->sendMail->sendBirthdayMail();
        } 
        finally {
            $lock->release();
        }
    }
}
