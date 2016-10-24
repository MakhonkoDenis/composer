<?php

if ( ! class_exists('Tools') ) {

	class Tools{
		/**
		 * @var $_config
		 */
		protected $config;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->config = $this->get_config();
		}

		/**
		 * @param string $config_name
		 * @param string $ext
		 *
		 * @return bool
		 */
		public function get_config( $config_name = 'config', $ext = '.json' ){
			$config_file = $config_name . $ext;

			if ( ! file_exists( $config_file ) ) {
				die( 'Dos not exist config file.' );

				return FALSE;
			}

			switch( $ext ) {
				case '.json':

					return json_decode( file_get_contents( $config_file ), true );
				break;

				default:
					die('Dos not exist extension config file.' );

					return false;
				break;
			}
		}
				/**
		 * @param string $config_name
		 * @param string $ext
		 *
		 * @return bool
		 */
		public function render_oprions( $options = array() ){
			$html = '';

			if ( ! empty( $options ) ) {
				foreach ( $options as $key => $value ) {
					$html .= '<option value="' . $key . '">' . $value . '</option>';
				}
			}

			return $html;
		}
	}
}

?>