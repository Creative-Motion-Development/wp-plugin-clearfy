<?php

class WCL_Plugin_Upgrader extends Plugin_Upgrader {
	
	/**
     * Download a package.
     *
     * @since 2.8.0
     *
     * @param string $package The URI of the package. If this is the full path to an
     *                        existing local file, it will be returned untouched.
     * @return string|WP_Error The full path to the downloaded package file, or a WP_Error object.
     */
    public function download_package( $package ) {
 
        /**
         * Filters whether to return the package.
         *
         * @since 3.7.0
         *
         * @param bool        $reply   Whether to bail without returning the package.
         *                             Default false.
         * @param string      $package The package file name.
         * @param WP_Upgrader $this    The WP_Upgrader instance.
         */
        $reply = apply_filters( 'upgrader_pre_download', false, $package, $this );
        if ( false !== $reply )
            return $reply;
 
        if ( ! preg_match('!^(http|https|ftp)://!i', $package) && file_exists($package) ) //Local file or remote?
            return $package; //must be a local file..
 
        if ( empty($package) )
            return new WP_Error('no_package', $this->strings['no_package']);
 
 
        $download_file = download_url( $package );
        $mime_type = mime_content_type( $download_file );
        
        if ( 'text/plain' == $mime_type ) {
			$this->result = new WP_Error( 'builder_error', __( file_get_contents( $download_file ), 'clearfy' ) );
			return $this->result;
		}
 
        if ( is_wp_error($download_file) )
            return new WP_Error('download_failed', $this->strings['download_failed'], $download_file->get_error_message());
 
        return $download_file;
    }
}
