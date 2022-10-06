<?php

/**
 * @package		Clicky Tracking Code - Plugin for Joomla!
 * @author		DeConf - https://deconf.com
 * @copyright	Copyright (c) 2010 - 2012 DeConf.com
 * @license		GNU/GPL license: https://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

class plgSystemClickyTrackingCode extends JPlugin {

	function onAfterRender() {
		$app = JFactory::getApplication();

		$user = JFactory::getUser();

		if ( ( $app->isClient('administrator') ) and ( $this->params->get( 'clicky_backend' ) ) ) {
			return;
		}

		if ( ( isset( $user->groups[8] ) || isset( $user->groups[7] ) ) and ( $this->params->get( 'clicky_admin' ) ) ) {
			return;
		}

		$tracking = "";
		$custom_tracking = "";

		$buffer = $app->getBody();

		if ( $user->username ) {

			if ( ( $this->params->get( 'clicky_usernames' ) ) and ( ( $this->params->get( 'clicky_emails' ) ) ) ) {

				$custom_tracking = "<script type=\"text/javascript\">
  var clicky_custom = {};
  clicky_custom.session = {
    username: '" . $user->username . "',
    email: '" . $user->email . "'
  };
</script>";
			} else if ( $this->params->get( 'clicky_usernames' ) ) {

				$custom_tracking = "<script type=\"text/javascript\">
  var clicky_custom = {};
  clicky_custom.session = {
    username: '" . $user->username . "'
  };
</script>";
			} else if ( $this->params->get( 'clicky_emails' ) ) {
				$custom_tracking = "<script type=\"text/javascript\">
  var clicky_custom = {};
  clicky_custom.session = {
    email: '" . $user->email . "'
  };
</script>";
			}
		}

		if ( ( $this->params->get( 'clicky_affiliate' ) ) and ! ( $app->isClient('administrator') ) ) {

			if ( $this->params->get( 'clicky_affiliate_id' ) )
				$affiliate_id = $this->params->get( 'clicky_affiliate_id' );
			else
				$affiliate_id = "66508224";
			$tracking .= "<center><a title=\"Web Analytics\" href=\"https://clicky.com/" . $affiliate_id . "\"><img alt=\"Web Analytics\" src=\"//static.getclicky.com/media/links/badge.gif\" border=\"0\" /></a></center>\n";
		}

		if ( $this->params->get( 'clicky_id' ) ) {
			$tracking .= "<script async src='//static.getclicky.com/" . $this->params->get( 'clicky_id' ) . ".js'></script>";
		}

		if ( $custom_tracking ) {
			$buffer = preg_replace( "/<\/body>/", "\n" . $custom_tracking . "\n</body>", $buffer );
		}

		if ( $tracking ) {
			$buffer = preg_replace( "/<\/body>/", "\n" . $tracking . "\n</body>", $buffer );
		}

		$app->setBody( $buffer );

		return;
	}
}
