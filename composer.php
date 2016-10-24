<?php
if ( ! class_exists('Composer') ) {

require_once __DIR__ . "/tools.php";

	class Composer extends Tools{
		/**
		 * @var $prefix
		 */
		protected $prefix = array(
			'__TM'		=> '',
			'__Tm'		=> '',
			'__tm'		=> '',
			': blank'	=> ': ',
		);

		/**
		 * @var $settihgs
		 */
		protected $settings;

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
		public function __construct( $args ) {
			parent::__construct();

			$this->config['download_folder'] = __DIR__ . $this->config['download_folder'];
			$this->config['deploy_folder'] = __DIR__ . $this->config['deploy_folder'];
			$this->config['time_limit'] = 10;

			$default = array(
				'theme_name'		=> "Me Cherry Theme",
				'theme_branches'	=> "blog",
			);

			$this->settings = array_merge( $default, $args );

			$this->clear_folder( $this->config['download_folder'] );

			$this->set_prefix( $this->settings[ 'theme_name' ] );

			$new_packed = $this->packed_theme( $this->settings );

			$this->download( $new_packed );
		}

		private function packed_theme( $settings ) {
			$zip_name			= $this->prefix[ '__tm' ] . '.' . $this->config['ext'];
			$theme_download		= $this->config['download_folder'] . $zip_name;

			if ( $this->create_archive( $theme_download ) ) {
				$theme_source		= $this->config['deploy_folder'] . $this->config['theme_name'] . '-'. $settings['theme_branches'] . '.' . $this->config['ext'];
				$framework_folder	= $this->config['framework_name'];
				$framework			= $this->config['deploy_folder'] . $framework_folder . '.' . $this->config['ext'];

				$zip				= new ZipArchive;
				$new_zip 			= new ZipArchive;
				$i					= 0;
				if (file_exists($theme_source) && file_exists($theme_download) ) {
					if ( $zip->open( $theme_source ) && $new_zip->open( $theme_download ) ) {

						while( $file_name = $zip->getNameIndex( $i ) ){

							$content = $this->parsed_content( $zip->getFromName( $file_name ) );

							$new_zip->addFromString( $file_name, $content );

							$i++;
						}

						$add_framework = $this->add_framework( array(
							'theme_zip'			=> $new_zip,
							'framework_archiv'	=> $framework,
							'framework_folder'	=> $framework_folder,
						) );

						if( ! $add_framework ){
							die("ERROR: Can't add framework.");
						}
					}
				}else{
					die("ERROR: Theme file missing.");
				}

				$zip->close();
				$new_zip->close();
			}

			return basename( $this->config['download_folder'] ) . '/' . $zip_name;
		}
		private function create_archive( $path ) {
			$handle = fopen( $path, 'w' );

			if( ! $handle && fclose($handle) ){
				return false;
			}

			return true;
		}

		private function add_framework( $args ) {
			$zip = new ZipArchive;
			$i = 0;

			if( $zip->open( $args['framework_archiv'] ) ) {

				while( $file_name = $zip->getNameIndex( $i ) ){
					$file = $zip->getFromName( $file_name );

					$args['theme_zip']->addFromString( $args['framework_folder'] . '/' . $file_name, $file );

					$i++;
				}
			}

			$zip->close();

			return true;
		}

		private function parsed_content( $content ) {
			foreach ( $this->prefix as $key => $value) {
				$content = str_replace( $key, $value, $content);
			}
			return $content;
		}

		private function clear_folder( $folder ) {
			$ignor = array(
				'',
				'index.php',
				'..',
				'.',
			 );
			$files = scandir ( $folder );

			foreach ($files as $file) {
				if ( ! array_search( $file, $ignor ) ) {
					$path = $folder . $file;

					$file_time = filectime ( $path );
					$time = time();
					$time_out = ( ( $time - $file_time ) / 60 );

					( ! is_dir( $path ) && $time_out > $this->config['time_limit'] ) ? unlink( $path ) : false ;
				}
			}
		}

		private function set_prefix( $name ) {
			$name = strtolower( $name );
			$name = preg_replace ( "/(\W|_)/mi", ' ', $name );

			$this->prefix[ ': blank' ]	= $this->prefix[ ': blank' ] . ucwords( $name );
			$this->prefix[ '__TM' ]		= str_replace( ' ', '_', strtoupper( $name ) );
			$this->prefix[ '__Tm' ]		= str_replace( ' ', '_', ucwords( $name ) );
			$this->prefix[ '__tm' ]		= str_replace( ' ', '_', $name );
		}

		private function download( $file ) {
			echo $file;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $args = array() ) {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $args );
			}
			return self::$instance;
		}
	}

	$theme_settings = array(
		'theme_name'		=> $_POST['form_data']['theme_name'],
		'theme_branches'	=> $_POST['form_data']['branch_name'],
	);

	Composer::get_instance( $theme_settings );
}
?>
