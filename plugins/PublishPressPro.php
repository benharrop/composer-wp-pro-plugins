<?php
/**
 * PublishPress Pro Plugin.
 *
 * @package Junaidbhura\Composer\WPProPlugins\Plugins
 */

namespace Junaidbhura\Composer\WPProPlugins\Plugins;

use Composer\Semver\Semver;
use Junaidbhura\Composer\WPProPlugins\Http;

/**
 * PublishPressPro class.
 */
class PublishPressPro {

	/**
	 * The version number of the plugin to download.
	 *
	 * @var string Version number.
	 */
	protected $version = '';

	/**
	 * The slug of which plugin to download.
	 *
	 * @var string Plugin slug.
	 */
	protected $slug = '';

	/**
	 * WpAiPro constructor.
	 *
	 * @param string $version
	 * @param string $slug
	 */
	public function __construct( $version = '', $slug = 'publishpress-planner-pro' ) {
		$this->version = $version;
		$this->slug    = $slug;
	}

	/**
	 * Get the download URL for this plugin.
	 *
	 * @return string
	 */
	public function getDownloadUrl() {
		$id  = 0;
		$env = null;
		/**
		 * Membership licensing.
		 */
		$license = ( getenv( 'PUBLISHPRESS_PRO_KEY' ) ?: null );
		$url     = ( getenv( 'PUBLISHPRESS_PRO_URL' ) ?: null );

		/**
		 * List of official plugins as of 2023-01-20.
		 */
		switch ( $this->slug ) {
			case 'publishpress-authors-pro':
				$id  = 7203;
				$env = 'AUTHORS';
				break;

			case 'publishpress-blocks-pro':
				$id  = 98972;
				$env = 'BLOCKS';
				break;

			case 'publishpress-capabilities-pro':
				$id  = 44811;
				$env = 'CAPABILITIES';
				break;

			case 'publishpress-checklists-pro':
				$id  = 6465;
				$env = 'CHECKLISTS';
				break;

			case 'publishpress-permissions-pro':
				$id  = 34506;
				$env = 'PERMISSIONS';
				break;

			case 'publishpress-planner-pro':
				$id  = 49742;
				$env = 'PLANNER';
				break;

			case 'publishpress-revisions-pro':
				$id  = 40280;
				$env = 'REVISIONS';
				break;

			case 'publishpress-series-pro':
				$id  = 110550;
				$env = 'SERIES';
				break;

			default:
				return '';
		}

		if ( $env ) {
			/**
			 * Use add-on licensing if available, otherwise use membership licensing.
			 */
			$license = ( getenv( "PUBLISHPRESS_{$env}_PRO_KEY" ) ?: $license );
			$url     = ( getenv( "PUBLISHPRESS_{$env}_PRO_URL" ) ?: $url );
		}

		$http     = new Http();
		$response = json_decode( $http->get( 'https://publishpress.com', array(
			'edd_action' => 'get_version',
			'license'    => $license,
			'item_id'    => $id,
			'url'        => $url,
			'version'    => $this->version,
		) ), true );

		if ( empty( $response['download_link'] ) ) {
			return '';
		}

		if ( empty( $response['new_version'] ) ) {
			return '';
		}

		if ( ! Semver::satisfies( $response['new_version'], $this->version ) ) {
			return '';
		}

		return $response['download_link'];
	}

}
