<?php
/*
 * Copyright (c) 2013 David Negrier
 * 
 * See the file LICENSE.txt for copying permission.
 */

namespace Mouf\Utils\I18n\Fine\Bridge;

use Mouf\Installer\PackageInstallerInterface;
use Mouf\MoufManager;
use Mouf\Actions\InstallUtils;

/**
 * The installer for the Symfony-Fine bridge
 */
class SymfonyFineBridgeInstaller implements PackageInstallerInterface {

	/**
	 * (non-PHPdoc)
	 * @see \Mouf\Installer\PackageInstallerInterface::install()
	 */
	public static function install(MoufManager $moufManager) {
	
		// These instances are expected to exist when the installer is run.
		$defaultTranslationService = $moufManager->getInstanceDescriptor('defaultTranslationService');
		$defaultLanguageDetection = $moufManager->getInstanceDescriptor('defaultLanguageDetection');
		
		// Let's create the instances.
		$symfonyTranslator = InstallUtils::getOrCreateInstance('symfonyTranslator', 'Mouf\\Utils\\I18n\\Fine\\Bridge\\SymfonyFineBridge', $moufManager);
		
		// Let's bind instances together.
		if (!$symfonyTranslator->getConstructorArgumentProperty('translator')->isValueSet()) {
			$symfonyTranslator->getConstructorArgumentProperty('translator')->setValue($defaultTranslationService);
		}
		if (!$symfonyTranslator->getConstructorArgumentProperty('detector')->isValueSet()) {
			$symfonyTranslator->getConstructorArgumentProperty('detector')->setValue($defaultLanguageDetection);
		}

		// Let's rewrite the MoufComponents.php file to save the component
		$moufManager->rewriteMouf();
	}
}
