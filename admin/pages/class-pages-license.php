<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Страница лицензирования плагина.
 *
 * Поддерживает режим работы с мультисаймами. Вы можете увидеть эту страницу в панели настройки сети.
 *
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, Github: https://github.com/alexkovalevv
 *
 * @copyright (c) 2018 Webraftic Ltd
 */
class WCL_LicensePage extends Wbcr_FactoryClearfy000_LicensePage {

	/**
	 * {@inheritdoc}
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 * @var string
	 */
	public $id = 'clearfy_license';

	/**
	 * {@inheritdoc}
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 * @var string
	 */
	public $page_parent_page;

	/**
	 * WCL_LicensePage constructor.
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 *
	 * @param \Wbcr_Factory000_Plugin $plugin
	 */
	public function __construct( Wbcr_Factory000_Plugin $plugin ) {
		$this->menu_title                  = __( 'License', 'robin-image-optimizer' );
		$this->page_menu_short_description = __( 'Product activation', 'robin-image-optimizer' );
		$this->plan_name                   = __( 'Clearfy Business', 'robin-image-optimizer' );

		parent::__construct( $plugin );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @author Alexander Kovalev <alex.kovalevv@gmail.com>
	 * @since  1.6.0
	 * @return string
	 */
	public function get_plan_description() {
		$description = '<p style="font-size: 16px;">' . __( '<b>Clearfy Business</b> is a paid package of components for the popular free WordPress plugin named Clearfy. You get access to all paid components at one price.', 'clearfy' ) . '</p>';
		$description .= '<p style="font-size: 16px;">' . __( 'Paid license guarantees that you can download and update existing and future paid components of the plugin.', 'clearfy' ) . '</p>';

		return $description;
	}
}