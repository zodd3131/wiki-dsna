<?php

###################################################
### PRETTY URL
###################################################

$actions = array( 'edit', 'watch', 'unwatch', 'delete','revert', 'rollback',
  'protect', 'unprotect', 'markpatrolled', 'render', 'submit', 'history', 'purge', 'info' );
 
foreach ( $actions as $action ) {
  $wgActionPaths[$action] = "/wiki/$1/$action";
}
$wgActionPaths['view'] = "/wiki/$1";
$wgArticlePath = $wgActionPaths['view'];

###################################################
### MAKE WIKI PRIVATE
###################################################

# Disable reading by anonymous users
$wgGroupPermissions['*']['read'] = false;

# But allow them to read e.g., these pages:
$wgWhitelistRead =  [ ]; #[ "Main Page", "Help:Contents" ];

# Disable anonymous editing
$wgGroupPermissions['*']['edit'] = false;

# Politique de modification des pages
$wgGroupPermissions['sysop']['editinterface'] = true;

# Prevent new user registrations except by sysops
$wgGroupPermissions['*']['createaccount'] = false;

###################################################
### Scribunto
###################################################
$wgScribuntoDefaultEngine = 'luastandalone';

###################################################
### Visual Editor
###################################################

// Enable by default for everybody
$wgDefaultUserOptions['visualeditor-enable'] = 1;

// Optional: Set VisualEditor as the default for anonymous users
// otherwise they will have to switch to VE
// $wgDefaultUserOptions['visualeditor-editor'] = "visualeditor";

// Don't allow users to disable it
$wgHiddenPrefs[] = 'visualeditor-enable';

$wgVirtualRestConfig['modules']['parsoid'] = array(
    // URL to the Parsoid instance
    // Use port 8142 if you use the Debian package
    'url' => 'http://parsoid:8000',
    // Parsoid "domain", see below (optional)
    'domain' => 'wiki',
    // Parsoid "prefix", see below (optional)
    //'prefix' => 'localhost'
);

$wgVisualEditorAvailableNamespaces = [
    "File" => false
];

// This feature requires a non-locking session store. The default session store will not work and
// will cause deadlocks (connection timeouts from Parsoid) when trying to use this feature. Only required for MediaWiki 1.26.x and earlier!
$wgSessionsInObjectCache = true;

// Forward users' Cookie: headers to Parsoid. Required for private wikis (login required to read).
// If the wiki is not private (i.e. $wgGroupPermissions['*']['read'] is true) this configuration
// variable will be ignored.
//
// WARNING: ONLY enable this on private wikis and ONLY IF you understand the SECURITY IMPLICATIONS
// of sending Cookie headers to Parsoid over HTTP. For security reasons, it is strongly recommended
// that $wgVirtualRestConfig['modules']['parsoid']['url'] be pointed to localhost if this setting is enabled.
$wgVirtualRestConfig['modules']['parsoid']['forwardCookies'] = true;

###################################################
### MAPS
###################################################
$GLOBALS['egMapsAvailableServices'] = [
    # 'googlemaps3', # On supprime complètement GoogleMaps (choix arbitraire)
    'openlayers',
    'leaflet',
];
$GLOBALS['egMapsDefaultService'] = 'leaflet';

###################################################
### Approved Revs
###################################################

$egApprovedRevsShowNotApprovedMessage=true; # Indicating unapproved pages
#$egApprovedRevsAutomaticApprovals = false; # les pages ne sont plus approuvées automatiquement à chaque modif quand on a les droits

###################################################
### BOOTSTRAP
###################################################
$wgHooks['SetupAfterCache'][]=function(){
	\Bootstrap\BootstrapManager::getInstance()->addAllBootstrapModules();
	return true;
};
$wgHooks['ParserAfterParse'][]=function( Parser &$parser, &$text, StripState &$stripState ){
	$parser->getOutput()->addModuleStyles( 'ext.bootstrap.styles' );
	$parser->getOutput()->addModuleScripts( 'ext.bootstrap.scripts' );
	return true;
};

###################################################
### SMW
###################################################

 enableSemantics( $_SERVER["MEDIAWIKI_DOMAIN"] );#enableSemantics( 'https://example.org/id/', true );
 $smwgNamespacesWithSemanticLinks[3000] = true;
 $smwgShowFactbox = SMW_FACTBOX_HIDDEN;
 $smwgUseCategoryHierarchy = true;
 $smwgEnabledSpecialPage = array( 'Ask', 'RunQuery' );
// $smwgEnabledCompatibilityMode = true; # ajout mike

###################################################
### Wiki Editor
###################################################
# Active l’utilisation de WikiEditor par défaut mais il est encore possible aux utilisateurs de le désactiver dans les préférences
$wgDefaultUserOptions['usebetatoolbar'] = 1;
# Active les assistants d’insertion de lien et de tableaux mais il reste encore possible pour les utilisateurs de les désactiver dans les préférences
$wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;
# Affiche les onglets Prévisualiser et Voir les changements
$wgDefaultUserOptions['wikieditor-preview'] = 1;

###################################################
### MISC
###################################################

# Tentative de résolution du pb lié à l'attribution de catégorie
$wgRunJobsAsync = false;

# Permettre d'ajouter tout type de code HTML dans la page (y compris forms, etc)
$wgRawHtml = true;

# Permettre d'éviter de recharger à chaque fois la sidebar et les styles associés
# lorsqu'on accède à une nouvelle page du wiki
//$wgEnableSidebarCache = true;

# Affichage des numéros de section directement dans les titres et sous-titres
$wgDefaultUserOptions['numberheadings'] = 1;

//$wgShowExceptionDetails= true;
//$wgDebugToolbar= true;
//$wgShowDBErrorBacktrace = true;


#########################
# Extensions disponibles
#########################
$wgFileExtensions[] = 'pdf';
$wgFileExtensions[] = 'svg';
$wgFileExtensions[] = 'png';
