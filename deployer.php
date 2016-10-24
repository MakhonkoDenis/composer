<?php
if ( ! class_exists('GitHubDeploy') ) {

	require_once __DIR__ . "/tools.php";

	class GitHubDeploy extends Tools{
		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			parent::__construct();

			$this->deploy();

			die( "SUCCESS: Create release." );
		}

		private function deploy() {
			$input_JSON = file_get_contents('php://input');
			$git_hub = json_decode( $input_JSON, true );

			if ( isset( $git_hub['release'] ) && !empty( $git_hub['release'] ) ){

				$flag = $git_hub["repository"]["name"] === $this->config['framework_name'];
				//$projects_lable = $this->get_lable( $git_hub['release']['tag_name'] );
				$framework_archiv = $this->config['framework_name'] . $projects_lable . '.' . $this->config['ext'];
				$archiv_name = $flag ? $framework_archiv : $this->config['theme_name'] . '-' . $git_hub['release']['target_commitish'] . '.' . $this->config['ext'] ;
				$deploy_path = __DIR__ . $this->config['deploy_folder'];
				$archiv_paht =  $deploy_path . $archiv_name;
				$download_archive =  $git_hub['release'][ $this->config['ext'] . 'ball_url'];

				if ( ! is_dir( $deploy_path ) ) {
					if ( ! mkdir( $deploy_path, 0755 ) ){
						die( 'ERROR: Error create project directory.');
					}
				}

				$github_files = $this->remote_query( array(
					'url'			=> $download_archive,
					'output_file'	=> $archiv_paht,
					'login'			=> $git_hub["repository"]["owner"]['login'],
					'token'			=> $this->config['token'],
					'user_agent'	=> $git_hub["repository"]["owner"]['login'],
				) );

				if ( $github_files ) {
					$zip = new ZipArchive;

					if( $zip->open( $archiv_paht ) ) {

						$i = 0;
						$folder_name = $zip->getNameIndex( $i );

						while( $file = $zip->getNameIndex( $i ) ){

							if( $this->check_deleted( $file ) ) {
								$zip->deleteName( $file );
							}else{
								$zip->renameIndex( $i, str_replace( $folder_name, '', $file ) );
							}

							$i++;
						}
						$zip->deleteName( $folder_name );

						$zip->close();
					}

				} else {
					die('ERROR: Error create archive "' . $archiv_paht . '".');
				}

			}else{
				die("ERROR: This is not release.");
			}
		}

		private function check_deleted( $file ){
			foreach ( $this->config['delete_files'] as $item ) {
				if ( strpos( $file, $item ) ) {
					return true;
				}
			}

			return false;
		}

		private function remote_query( $args ) {
			$handle = curl_init();

			if( $handle ) {
				$url_args = '?login=' . $args['login'] . '&access_token=' . $args['token'];

				$curl_options = array(
					CURLOPT_SSL_VERIFYHOST	=> false,
					CURLOPT_SSL_VERIFYPEER	=> false,
					//CURLOPT_CAINFO			=> dirname(__FILE__) . '/certificates/ca-bundle.crt',

					/*CURLOPT_PROXYTYPE		=> CURLPROXY_HTTP,
					CURLOPT_PROXY			=> '192.168.9.111',
					CURLOPT_PROXYPORT		=> '3128',*/

					CURLOPT_URL				=> $args['url'] . $url_args,
					CURLOPT_HTTPAUTH		=> CURLAUTH_BASIC,
					CURLOPT_USERPWD			=> join( ':', array( 'token', $args['token'] ) ),
					CURLOPT_HTTPHEADER => array(
						'Authorization'	=> join( ' ', array( 'token', $args['token'] ) ),
						'Content-Type'	=> 'application/octet-stream',
						'Accept'		=> 'application/vnd.github.v3+json'
					),
					CURLOPT_AUTOREFERER		=> true,
					CURLOPT_FOLLOWLOCATION	=> true,
					CURLOPT_USERAGENT		=> $args['user_agent'],
					CURLOPT_RETURNTRANSFER	=> true,
					CURLOPT_FILE			=> fopen( $args['output_file'], 'w' ),
				);

				curl_setopt_array( $handle, $curl_options );

				curl_exec( $handle );

				if ( isset( $args[ 'output_file' ] ) ) {
					fclose( $args[ 'output_file' ] );
				}

				$response_info = curl_getinfo( $handle, CURLINFO_HTTP_CODE );

				if ( $response_info !== 200 ) {
					$error = curl_error( $handle ).' ( '.curl_errno( $handle ).' ) ';

					die ( $error );
				}
			}
			curl_close($handle);

			return true;
		}

		private function get_lable( $string ) {
			return strtolower( preg_replace( '/[v]?[\d\.]+[v]?/', '', $string ) );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
	}
	GitHubDeploy::get_instance();
}
?>
