<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 4/21/2018
 * Time: 11:20 AM
 */

namespace Haosf_Social_Proof_Toaster;


class Product_Virtual_Social_Proof_Toast extends Product_Social_Proof_Toast {
	protected function get_message_top() {
		$names_repository = Plugin::instance()->setting( 'names_repository', require 'names-repository.php' );
		$addresses_repository = Plugin::instance()->setting( 'addresses_repository', require_once 'address-repository.php');
		shuffle( $names_repository);
		$person_name      = Plugin::instance()->session->remember( 'uri', 'random_message_top', function () use ( $names_repository ) {
			return $names_repository[0];
		});

		$placeholder = <<<HTML
<span class="haosf_toast_person_name">%s</span> from <span class="haosf_toast_person_address">%s</span>
HTML;

		$address              = $addresses_repository[0];

		return sprintf($placeholder, $person_name, $address);
	}

	protected function get_close_image() {
		return false;
	}

	protected function get_image() {
		return Plugin::instance()->session->remember( 'uri', 'image', function () {
			$set          = [ '', 'set2', 'set3', 'set4' ][ rand( 1, 4 ) ];
			$randomSlug   = rand( 1, 100 );
			$imageSource  = "https://robohash.org/$randomSlug.png?size=100x100&set=$set";
			return $imageSource;
		});
	}

	protected function get_message_middle() {
		$format = Plugin::instance()->setting('product_virtual_social_proof_middle_message', __("Just bought <span>%s</span>"));

		return sprintf( $format, $this->get_middle_subject());
	}

	protected function get_message_bottom() {
		$format = Plugin::instance()->setting('product_virtual_social_proof_middle_message', __("Total sales: %s"));
		$total_sales = $this->get_virtual_product_order_toast_total_sales();

		return sprintf($format, $total_sales);
	}

	private function get_virtual_product_order_toast_total_sales() {
		$post_virtual_total_sales = get_post_meta( $this->get_product()->get_id(), 'haosf_virtual_total_sales', true );
		if(empty($post_virtual_total_sales)) {
			$post_virtual_total_sales = do_shortcode(Plugin::instance()->setting( 'product_virtual_total_sales', '[haosf_random range="1000,2000" session_remember=1 session_remember_key="uri" session_remember_salt=0]' ));
		}

		return $post_virtual_total_sales;
	}

	public function get_middle_subject() {
		$subject = '[haosf_product current=1]';
		$subject_setting_per_post = get_post_meta( $this->get_product()->get_id(),
			'haosf_virtual_social_proof_subject' );
		if( $subject_setting_per_post ) {
			return do_shortcode( $subject_setting_per_post);
		}
		return do_shortcode(Plugin::instance()->setting('product_virtual_subject_format', $subject));
	}
}