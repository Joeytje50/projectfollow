<?php

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'ProjectFollow' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['ProjectFollow'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['ProjectFollow'] = __DIR__ . '/ProjectFollow.i18n.alias.php';
	wfWarn(
		'Deprecated PHP entry point used for PageFollow extension. Please use wfLoadExtension ' .
		'instead, see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return true;
} else {
	die( 'This version of the ProjectFollow extension requires MediaWiki 1.25+' );
}
