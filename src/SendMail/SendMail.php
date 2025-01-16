<?php

namespace Arminfrey\ContaoGbmBundle\SendMail;

use Contao\Backend;
use Contao\BackendTemplate; 
use Contao\System;
use Contao\StringUtil;
use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SendMail
{
	private Connection $connection;
	const DEFAULT_LANGUAGE = 'de';
	private $logger;

    public function __construct(Connection $connection,LoggerInterface $logger)
    {
        $this->connection = $connection;
	$this->logger     = $logger;
    }


  public function sendBirthdayMailManually()
	{
		$isBackend = System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? \Symfony\Component\HttpFoundation\Request::create(''));
		if ($isBackend)
		{
			$result = $this->sendBirthdayMail();
			
			// Create template object
			$objTemplate = new BackendTemplate('be_geburtstagsmail');
			$cleanedUrl = str_replace('&key=sendBirthdayMail', '', System::getContainer()->get('request_stack')->getCurrentRequest());
			var_dump($cleanedUrl);
			var_dump(System::getContainer()->get('request_stack')->getCurrentRequest());
			$cleanedUrl = str_replace('&', '&amp;', $cleanedUrl);
			var_dump($cleanedUrl);
			$escapedTitle = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);
			$objTemplate->backLink = '<a href="' . $cleanedUrl . '" class="header_back" title="' . $escapedTitle . '" accesskey="b">' . $escapedTitle . '</a>';
			//$objTemplate->backLink = '<a href="'.\ampersand(str_replace('&key=sendBirthdayMail', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>';
			$objTemplate->headline = $GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['headline'];
			$objTemplate->sendingHeadline = $GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['sendingHeadline'];
			$objTemplate->success = sprintf($GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['successMessage'], $result['success']);
			
			$objTemplate->failed = is_array($result['failed']) && sizeof($result['failed']) > 0;
			$objTemplate->failureMessage = sprintf(
    					$GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['failureMessage'],
    					is_array($result['failed']) ? sizeof($result['failed']) : 0  // Default to 0 if not an array
			);
			$objTemplate->failureTableHead = $GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['failureTableHead'];
			$objTemplate->failures = $result['failed'] ?? [];  // Use null coalescing operator for safety
			$objTemplate->failureInfo = $GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['failureInfo'];
						
			$objTemplate->aborted = is_array($result['aborted']) && sizeof($result['aborted']) > 0;
			$objTemplate->abortionMessage = sprintf(
    				$GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['abortionMessage'],
    				is_array($result['aborted']) ? sizeof($result['aborted']) : 0  // Default to 0 if not an array
			);
			$objTemplate->abortionTableHead = $GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['abortionTableHead'];
			$objTemplate->abortions = $result['aborted'] ?? [];  // Use null coalescing operator for safety
			$objTemplate->abortionInfo = $GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['abortionInfo'];
			
			if ($GLOBALS['TL_CONFIG']['birthdayMailerDeveloperMode'])
			{
				$objTemplate->developerMessage = sprintf($GLOBALS['TL_LANG']['tl_geburtstagsmail']['manualExecution']['developerMessage'], $GLOBALS['TL_CONFIG']['birthdayMailerDeveloperModeEmail']);
			}
			
			//return $this->replaceInsertTags($objTemplate->parse());
			return $objTemplate->parse();
		}
		return;
	}

	/**
	 * Sends the birthday emails.
	 */
	public function sendBirthdayMail()
	{
		
		$alreadySendTo = array();
		$notSendCauseOfError = array();
		$notSendCauseOfAbortion = array();
		$config = $this->connection->fetchAllAssociative("SELECT tl_member.*, "
			. "tl_member_group.name as memberGroupName, tl_member_group.disable as memberGroupDisable, tl_member_group.start as memberGroupStart, tl_member_group.stop as memberGroupStop, "
			. "tl_geburtstagsmail.sender as mailSender, tl_geburtstagsmail.senderName as mailSenderName, tl_geburtstagsmail.mailCopy as mailCopy, tl_geburtstagsmail.mailBlindCopy as mailBlindCopy, "
			. "tl_geburtstagsmail.mailUseCustomText as mailUseCustomText, tl_geburtstagsmail.mailTextKey as mailTextKey "
			. "FROM tl_member "
			. "JOIN tl_member_group ON tl_member_group.id = CONVERT(substr(tl_member.groups,-4,1) using UTF8) "
			. "JOIN tl_geburtstagsmail ON tl_geburtstagsmail.membergroup = tl_member_group.id "
			. "WHERE tl_member.disable = 0 "
			. "AND DATE_FORMAT(CURRENT_DATE(), '%d.%c') = DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(0), interval tl_member.dateOfBirth second), '%d.%c') "
			. "ORDER BY tl_member.id, tl_geburtstagsmail.priority DESC");
		
		foreach ($config as $conf) 
		{
				if(($GLOBALS['TL_CONFIG']['birthdayMailerDeveloperMode'] && 
				   $GLOBALS['TL_CONFIG']['birthdayMailerDeveloperModeIgnoreDate']) 
				&& ($this->allowSendingDuplicates($alreadySendTo, $conf)))
				{
				// now check via custom hook, if sending should be aborted
				$blnAbortSendMail = false;
				if (isset($GLOBALS['TL_HOOKS']['birthdayMailerAbortSendMail']) && is_array($GLOBALS['TL_HOOKS']['birthdayMailerAbortSendMail']))
				{
					foreach ($GLOBALS['TL_HOOKS']['birthdayMailerAbortSendMail'] as $callback)
					{
						$this->import($callback[0]);
						$blnAbortSendMail = $this->{$callback[0]}->{$callback[1]}($config, $blnAbortSendMail);
					}
				}
				
				if (!$blnAbortSendMail)
				{
					if ($this->sendMail($conf))
					{
						$alreadySendTo[] =  $conf->id;
					}
					else
					{
						$notSendCauseOfError[] =  array('id' => $conf->id, 'firstname' => $conf->firstname, 'lastname' => $conf->lastname, 'email' => $conf->email);
					}
				}
				else
				{
					$notSendCauseOfAbortion[] =  array('id' => $conf->id, 'firstname' => $conf->firstname, 'lastname' => $conf->lastname, 'email' => $conf->email);
				}
			}
		}

		$this->logger->info('BirthdayMailer: Daily sending of birthday mail finished. Send ' . sizeof($alreadySendTo) . ' emails. '
							. sizeof($notSendCauseOfError) . ' emails could not be send due to errors. '
							. sizeof($notSendCauseOfAbortion) . ' emails were aborted due to custom hooks. See birthdaymails.log for details.', array('contao' => new ContaoContext(__FUNCTION__, ContaoContext::GENERAL)));
		
		return array('success' => sizeof($alreadySendTo), 'failed' => $notSendCauseOfError, 'aborted' => $notSendCauseOfAbortion);
	}
	
	/**
	 * Get the text for specific types for the email. Fallback ist to 'default' if no text is set.
	 * FALLBACK Chain:
	 * 		1. check, if there is a text for the specified textKey and language (search in system/config/langconfig.php)
	 *		2. if nothing found, check, if there is a text for the specified textKey and 'en' (search in system/config/langconfig.php)
	 *		3. if nothing found, get default text in specified language
	 *		4. if nothing found, get default text in language 'en'
	 */
	private function getEmailText ($textType, $config, $language)
	{
		$text = "";

		if ($config->mailUseCustomText)
		{
			$text = $GLOBALS['TL_LANG']['Geburtstagsmail']['mail'][$config->mailTextKey][$textType][$language];
			
			if (strlen($text) == 0 && $language != self::DEFAULT_LANGUAGE)
			{
				$text = $GLOBALS['TL_LANG']['Geburtstagsmail']['mail'][$config->mailTextKey][$textType][self::DEFAULT_LANGUAGE];
			}
		}

		if (strlen($text) == 0)
		{
			$text = $GLOBALS['TL_LANG']['Geburtstagsmail']['mail']['default'][$textType];
		}

		$textReplaced = $this->replaceBirthdayMailerInsertTags($text, $config, $language);
		
		if ($textReplaced)
		{
			return $textReplaced;
		}
		
		return $text;
	}
	
	/**
	 * Send an email.
	 * @return boolean
	 */
	private function sendMail($config)
	{
		$language = $config->language;
		if (strlen($language) == 0)
		{
			$language = self::DEFAULT_LANGUAGE;
		}
		
		System::loadLanguageFile('Geburtstagsmailer', $language);
		
		$emailSubject = $this->getEmailText('subject', $config, $language);
		$emailText = $this->getEmailText('text', $config, $language);
		$emailHtml = $this->getEmailText('html', $config, $language);
	
		if ($GLOBALS['TL_CONFIG']['birthdayMailerDeveloperMode'] || $GLOBALS['TL_CONFIG']['birthdayMailerLogDebugInfo'])
		{
			$mailTextUsageOutput = $config->mailUseCustomText ? 'yes' : 'no';
			$this->logger->info('Geburtstagsmailer: These are additional debugging information that will only be logged in developer mode or if debugging is enabled.'
									 . ' | Userlanguage = ' . $config->language
								   . ' | used language = ' . $language
								   . ' | mailTextUsage = ' . $mailTextUsageOutput
								   . ' | mailTextKey = ' . $config->mailTextKey
								   . ' | email = ' . $config->email
								   . ' | subject = ' . $emailSubject
								   . ' | text = ' . $emailText
								   . ' | html = ' . $emailHtml, array('contao' => new ContaoContext(__FUNCTION__, ContaoContext::GENERAL)));
			
		}
		
		$objEmail = new \Email();

		$objEmail->logFile = 'birthdaymails.log';
		
		$objEmail->from = $config->mailSender;
		if (strlen($config->mailSenderName) > 0)
		{
			$objEmail->fromName = $config->mailSenderName;
		}
		$objEmail->subject = $emailSubject;
		$objEmail->text = $emailText;
		$objEmail->html = $emailHtml;
		
		try
		{
			$emailTo = $config->email;
			
			if ($GLOBALS['TL_CONFIG']['birthdayMailerDeveloperMode'])
			{
				$emailTo = $GLOBALS['TL_CONFIG']['birthdayMailerDeveloperModeEmail'];
			}
			else
			{
				if (strlen($config->mailCopy) > 0)
				{
					$emailCC = trimsplit(',', $config->mailCopy);
					$objEmail->sendCc($emailCC);
				}
				
				if (strlen($config->mailBlindCopy) > 0)
				{
					$emailBCC = trimsplit(',', $config->mailBlindCopy);
					$objEmail->sendBcc($emailBCC);
				}
				
				$emailTo = $config->email;
			}
			return $objEmail->sendTo($emailTo);
		}
		catch (Swift_RfcComplianceException $e)
		{
			return false;
		}
	}

	/**
	 * Checks if the member is active.
	 * @return boolean
	 */
	/*private function isMemberActive($config)
	{
		if ($config->disable ||
			(strlen($config->start) > 0 &&
			$config->start > time()) ||
			(strlen($config->stop) > 0 &&
			$config->stop < time()))
		{
			return false;
		}
		return true;
	}*/

	/**
	 * Checks if the associated group is active.
	 * @return boolean
	 */
	/*private function isMemberGroupActive($config)
	{
		if ($config->memberGroupDisable ||
			(strlen($config->memberGroupStart) > 0 &&
			$config->memberGroupStart > time()) ||
			(strlen($config->memberGroupStop) > 0 &&
			$config->memberGroupStop < time()))
		{
			return false;
		}
		return true;
	}*/
	
	/**
	 * Checks if sending duplicate emails is allowed.
	 * @return boolean
	 */
	private function allowSendingDuplicates($alreadySendTo, $config)
	{
		if (!$GLOBALS['TL_CONFIG']['birthdayMailerAllowDuplicates'] && in_array($config->id, $alreadySendTo))
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Delete an according configuration, if the member group is deleted.
	 */
	public function deleteConfiguration(DataContainer $dc)
	{
		$this->connection->executeStatement('DELETE FROM tl_geburtstagsmail WHERE memberGroup = ?', [$dc->id]);
	}
	
	/**
	 * Replaces all insert tags for the email text.
	 */
	private function replaceBirthdayMailerInsertTags ($text, $config, $language)
	{
		$textArray = preg_split('/\{\{([^\}]+)\}\}/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		
		for ($count = 0; $count < count($textArray); $count++)
		{
			$parts = explode("::", $textArray[$count]);
			switch ($parts[0])
			{
				case 'birthdaychild':
					switch ($parts[1])
					{
						case 'salutation':
							$salutation = $this->getSalutation($config, $language, 'salutation_' . $config->gender);
							if (strlen($salutation) == 0)
							{
								$salutation = $this->getSalutation($config, $language, 'salutation');
							}
							$textArray[$count] = $salutation;
							break;
							
						case 'name':
							$textArray[$count] = trim($config->firstname . ' ' . $config->lastname);
							break;
							
						case 'groupname':
							$textArray[$count] = trim($config->memberGroupName);
							break;
							
						case 'password':
							// do not allow extracting the password
							$textArray[$count] = "";
							break;
							
						case 'age':
							$textArray[$count] = (date("Y") - date("Y", $config->dateOfBirth));
							break;
							
						default:
							$textArray[$count] = $config->{$parts[1]};
							break;
					}
					break;
					
				case 'birthdaymailer':
					switch ($parts[1])
					{
						case 'email':
							$textArray[$count] = trim($config->mailSender);
							break;
							
						case 'name':
							$textArray[$count] = trim($config->mailSenderName);
							break;
					}
					break;
			}
		}
		
		return implode('', $textArray);
	}
	
	/**
	 * Get the text for specific types. Fallback ist to 'default' if no text is set.
	 * FALLBACK Chain:
	 * 		1. check, if there is a text for the specified textKey and language (search in system/config/langconfig.php)
	 *		2. if nothing found, check, if there is a text for the specified textKey and 'en' (search in system/config/langconfig.php)
	 *		3. if nothing found, get default text in specified language
	 *		4. if nothing found, get default text in language 'en'
	 */
	private function getSalutation($config, $language, $textType)
	{
		$text = "";

		if ($config->mailUseCustomText)
		{
			$text = $GLOBALS['TL_LANG']['Geburtstagsmail']['mail'][$config->mailTextKey][$textType][$language];
			
			if (strlen($text) == 0 && $language != self::DEFAULT_LANGUAGE)
			{
				$text = $GLOBALS['TL_LANG']['Geburtstagsmail']['mail'][$config->mailTextKey][$textType][self::DEFAULT_LANGUAGE];
			}
		}

		if (strlen($text) == 0)
		{
			$text = $GLOBALS['TL_LANG']['Geburtstagsmail']['mail']['default'][$textType];
		}
    		return $text;
	}
}
