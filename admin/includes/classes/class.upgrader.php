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
		public function download_package($package)
		{

			/**
			 * Filters whether to return the package.
			 *
			 * @since 3.7.0
			 *
			 * @param bool $reply Whether to bail without returning the package.
			 *                             Default false.
			 * @param string $package The package file name.
			 * @param WP_Upgrader $this The WP_Upgrader instance.
			 */
			$reply = apply_filters('upgrader_pre_download', false, $package, $this);
			if( false !== $reply ) {
				return $reply;
			}

			if( !preg_match('!^(http|https|ftp)://!i', $package) && file_exists($package) ) //Local file or remote?
			{
				return $package;
			} //must be a local file..

			if( empty($package) ) {
				return new WP_Error('no_package', $this->strings['no_package']);
			}

			$download_file = download_url($package, 1000);

			if( function_exists('mime_content_type') ) {
				$mime_type = mime_content_type($download_file);
			} else {
				$mime_type = $this->mimeContentType($download_file);
			}

			if( WbcrFactoryClearfy000_Helpers::strContains($mime_type, 'text/plain') ) {
				$this->result = new WP_Error('builder_error', __(file_get_contents($download_file), 'clearfy'));

				return $this->result;
			}

			if( is_wp_error($download_file) ) {
				return new WP_Error('download_failed', $this->strings['download_failed'], $download_file->get_error_message());
			}

			return $download_file;
		}

		public function mimeContentType($filename)
		{
			$mime_types = array(
				'txt' => 'text/plain',
				'htm' => 'text/html',
				'html' => 'text/html',
				'php' => 'text/html',
				'css' => 'text/css',
				'js' => 'application/javascript',
				'json' => 'application/json',
				'xml' => 'application/xml',
				'swf' => 'application/x-shockwave-flash',
				'flv' => 'video/x-flv',
				// images
				'png' => 'image/png',
				'jpe' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
				'gif' => 'image/gif',
				'bmp' => 'image/bmp',
				'ico' => 'image/vnd.microsoft.icon',
				'tiff' => 'image/tiff',
				'tif' => 'image/tiff',
				'svg' => 'image/svg+xml',
				'svgz' => 'image/svg+xml',
				// archives
				'zip' => 'application/zip',
				'rar' => 'application/x-rar-compressed',
				'exe' => 'application/x-msdownload',
				'msi' => 'application/x-msdownload',
				'cab' => 'application/vnd.ms-cab-compressed',
				// audio/video
				'mp3' => 'audio/mpeg',
				'qt' => 'video/quicktime',
				'mov' => 'video/quicktime',
				// adobe
				'pdf' => 'application/pdf',
				'psd' => 'image/vnd.adobe.photoshop',
				'ai' => 'application/postscript',
				'eps' => 'application/postscript',
				'ps' => 'application/postscript',
				// ms office
				'doc' => 'application/msword',
				'rtf' => 'application/rtf',
				'xls' => 'application/vnd.ms-excel',
				'ppt' => 'application/vnd.ms-powerpoint',
				// open office
				'odt' => 'application/vnd.oasis.opendocument.text',
				'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
			);

			$ext = strtolower(array_pop(explode('.', $filename)));
			if( array_key_exists($ext, $mime_types) ) {
				return $mime_types[$ext];
			} elseif( function_exists('finfo_open') ) {
				$finfo = finfo_open(FILEINFO_MIME);
				$mimetype = finfo_file($finfo, $filename);
				finfo_close($finfo);

				return $mimetype;
			} else {
				return 'application/octet-stream';
			}
		}
	}
