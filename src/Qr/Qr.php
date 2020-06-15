<?php namespace Mmaaikel\HtmlQrcodeMaker\Qr;

	use Throwable;
	use Picqer;
	use chillerlan;
	
	class Qr {
		
		const BARCODE_GENERATE_TYPE_SVG = 'svg';
		const BARCODE_GENERATE_TYPE_PNG = 'png';
		const BARCODE_GENERATE_TYPE_HTML = 'html';
		
		public static function create_barcode_image( $code, $options = [] ) {
			$type = self::get( $options, 'type', self::BARCODE_GENERATE_TYPE_PNG );
			$scale = self::get( $options, 'scale', 5 );
			$style = self::get( $options, 'style', 'light' );
			
			switch( $type ) {
				case self::BARCODE_GENERATE_TYPE_SVG:
					$qr_output_type = chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG;
					break;
				
				case self::BARCODE_GENERATE_TYPE_PNG:
					$qr_output_type = chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG;
					break;
				
				case self::BARCODE_GENERATE_TYPE_HTML:
				default:
					$qr_output_type = chillerlan\QRCode\QRCode::OUTPUT_MARKUP_HTML;
			}
			
			switch( $style ) {
				case 'dark':
					$qr_primary = '#fff';
					$qr_secondary = '#fff';
					break;
					
				default:
					$qr_primary = '#000';
					$qr_secondary = '#fff';
			}
			
			$qrCode = new chillerlan\QRCode\QRCode( new \chillerlan\QRCode\QROptions( [
				'outputType'   	=> $qr_output_type,
				'eccLevel'     	=> chillerlan\QRCode\QRCode::ECC_L,
				'scale'        	=> $scale,
				'textDark' 		=> $qr_primary,
				'textLight'		=> $qr_secondary,
				'markupDark' 	=> $qr_primary,
				'markupLight'	=> $qr_secondary,
			] ) );
			
			$generated_barcode = $qrCode->render( $code );
			
			// When HTML add the qrcode class
			if( $type == self::BARCODE_GENERATE_TYPE_HTML )
			{
				$generated_barcode = sprintf( '<div class="%s">%s</div>', 'qrcode', $generated_barcode );
			}
			
			return self::create_barcode_image_img( $generated_barcode, $type );
		}
		
		private static function create_barcode_image_img( $barcode, $type ) {
			switch( true ) {
				case self::string_starts_with( $barcode, 'data:' ):
					return sprintf('<img src="%s">', $barcode );
				
				case self::string_equals( $type, 'png' ):
					$barcode_base64 = base64_encode( $barcode );
					return sprintf('<img src="data:image/png;base64,%s">', $barcode_base64 );
				
				default:
					return $barcode;
			}
		}
		
		private static function string_equals( $value1, $value2 ) {
			return $value1 == $value2;
		}
		
		private static function string_starts_with( $haystack, $needle ) {
			$length = strlen($needle);
			return (substr($haystack, 0, $length) === $needle);
		}
		
		private static function get( $array, $variable, $return_missing = false ) {
			return isset( $array[ $variable ] ) && ( !empty( $array[ $variable ] ) || ( is_string( $array[ $variable ] ) && $array[ $variable ] === '0' ) || is_int( $array[ $variable ] ) || is_bool( $array[ $variable ] ) ) ? $array[ $variable ] : $return_missing;
		}
	}
