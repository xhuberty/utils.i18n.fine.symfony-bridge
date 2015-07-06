<?php
namespace Mouf\Utils\I18n\Fine\Bridge;

use Symfony\Component\Translation\TranslatorInterface;
use Mouf\Utils\I18n\Fine\TranslatorInterface as MoufTranslatorInterface;
use Mouf\Utils\I18n\Fine\Language\FixedLanguageDetection;
use Mouf\Utils\I18n\Fine\Language\CascadingLanguageDetection;
use Mouf\Utils\I18n\Fine\LanguageDetectionInterface;

/**
 * This class is a bridge between a FINE translation service and Symfony.
 * Using this bridge, you can use FINE in a Symfony component.
 * 
 * Note: there is not a 1..1 mapping between Fine and Symfony.
 * Fine expects parameters to be passed as a series of parameters in the function while
 * Symfony takes an associative array.
 * 
 * - the transChoice is simulated by concatenating the id and the number
 * So $service->transChoice("key", 2) will be translated in Fine in $service->getMessage("key2") 
 */
class SymfonyFineBridge implements TranslatorInterface {
	
	/**
	 * @var MoufTranslatorInterface
	 */
	private $translator;
	
	/**
	 * 
	 * @var LanguageDetectionInterface
	 */
	private $detector;
	
	/**
	 * Used if the "setLocale" method is called.
	 * 
	 * @var FixedLanguageDetection
	 */
	private $fixedLanguageDetection;
	
	/**
	 * Used to cascade FixedLanguageDetection with the provide language detection.
	 *
	 * @var CascadingLanguageDetection
	 */
	private $cascadingLanguageDetection;
	
	public function __construct(MoufTranslatorInterface $translator, LanguageDetectionInterface $detector) {
		$this->translator = $translator;
		$this->detector = $detector;
		$this->fixedLanguageDetection = new FixedLanguageDetection();
		$this->cascadingLanguageDetection = new CascadingLanguageDetection();
		$this->cascadingLanguageDetection->languageDetectionServices = [
			$this->fixedLanguageDetection,
			$this->detector
		];
	}
	
	
	/* (non-PHPdoc)
	 * @see \Symfony\Component\Translation\TranslatorInterface::trans()
	 */
	public function trans($id, array $parameters = array(), $domain = null, $locale = null) {
		// Note: $domain is ignored.
		// Note: $locale is ignored.
		array_unshift($parameters, $id);
		// FIXME: hasTranslation is not part of the LanguageTranslationInterface interface
		if ($this->translator->hasTranslation($id)) {
			return $this->translator->getTranslation($id, $parameters);
		} else {
			return self::interpolate($id, $parameters);
		}
		
	}

	/* (non-PHPdoc)
	 * @see \Symfony\Component\Translation\TranslatorInterface::transChoice()
	 */
	public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null) {
		return $this->trans($id.$number, $parameters, $domain, $locale);
	}

	/* (non-PHPdoc)
	 * @see \Symfony\Component\Translation\TranslatorInterface::setLocale()
	 */
	public function setLocale($locale) {
		$this->fixedLanguageDetection->setLanguage($locale);
	}

	/* (non-PHPdoc)
	 * @see \Symfony\Component\Translation\TranslatorInterface::getLocale()
	 */
	public function getLocale() {
		return $this->cascadingLanguageDetection->getLanguage();
	}
	
	/**
	 * Interpolates context values into the message placeholders.
	 */
	private static function interpolate($message, array $context = array())
	{
	    // build a replacement array with braces around the context keys
	    $replace = array();
	    foreach ($context as $key => $val) {
	        $replace['%' . $key . '%'] = $val;
	    }
	
	    // interpolate replacement values into the message and return
	    return strtr($message, $replace);
	}
}