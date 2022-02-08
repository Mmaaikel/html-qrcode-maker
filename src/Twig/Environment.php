<?php namespace Mmaaikel\HtmlQrcodeMaker\Twig;

	use Mmaaikel\HtmlQrcodeMaker\Qr\Qr;
	use Twig;
	use Twig\Extension\DebugExtension;
	use Twig\Loader\ArrayLoader;

	class Environment {
		
		public $twig;
		private $assetPath;
		
		/**
		 * Twig constructor.
		 *
		 * @param $assetPath
		 */
		public function __construct( $assetPath ) {
			$this->assetPath = $assetPath;
			
			$this->twig = new Twig\Environment( new ArrayLoader(), [
				'strict_variables' => false,
				
				'debug' => true,
			] );
			
			// Enable compression regardless of Twig's debug setting
			$this->twig->addExtension(new DebugExtension());
			
			// HTML
			$this->twig->addFunction( new Twig\TwigFunction( 'html', function( $html ) {
				return $html;
			}, [ 'is_safe' => ['html'] ]));
			
			$this->twig->addFunction( new Twig\TwigFunction('barcode', function( $code, $options = [] ) {
				return Qr::create_barcode_image( $code, $options );
			}, [ 'is_safe' => ['html'] ] ) );
			
			// Get image data
			$this->twig->addFunction( new Twig\TwigFunction( 'image_data', function( $image ) {
				return $this->image_data( $this->create_image_path( $image ) );
			}));
		}
		
		private function create_image_path( $image ) {
			return str_replace( "//", "/", $this->assetPath .'/'. $image );
		}
		
		private function image_data( $image ) {
			if( is_file( $image ) )
			{
				// Read image path, convert to base64 encoding
				$image_data = base64_encode( file_get_contents( $image ) );
				
				$mime = self::get_mime_type( $image );
				
				// Format the image SRC:  data:{mime};base64,{data};
				return sprintf('data:%s;base64,%s', $mime, $image_data );
			}
			
			return false;
		}
		
		private function get_mime_type( $image ) {
			$base_name = basename( $image );
			$file_extension = strtolower( pathinfo( $base_name, PATHINFO_EXTENSION) );
			
			switch( $file_extension ) {
				case 'svg':
					return 'image/svg+xml';
				
				default:
					return mime_content_type( $image );
			}
		}
		
		/**
		 * @return Twig\Environment
		 */
		public function getTwig() {
			return $this->twig;
		}
	}
