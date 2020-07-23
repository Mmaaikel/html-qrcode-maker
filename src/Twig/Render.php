<?php namespace Mmaaikel\HtmlQrcodeMaker\Twig;

	use Twig\Loader\FilesystemLoader;
	
	class Render {
		
		private $path;
		private $storage_path;
		private $environment;
		
		/**
		 * Render constructor.
		 *
		 * @param $path
		 * @param array $extra_paths
		 *
		 * @throws \Twig\Error\LoaderError
		 */
		public function __construct( $path, $extra_paths = [] ) {
			$this->path = $path;
			$this->environment = new Environment( $this->path );
			$this->storage_path = __DIR__ .'/../../storage/';
			
			if( !is_dir( $this->storage_path ) ) {
				mkdir( $this->storage_path, 0755 );
			}
			
			// Create the loader
			$loader = new FilesystemLoader( $this->path );
			$loader->addPath( $this->path );
			
			// Add extra paths
			if( !empty( $extra_paths ) )
			{
				foreach( $extra_paths as $extra_path )
				{
					$loader->addPath( $extra_path );
				}
			}
			
			// Set the loader
			$this->environment->twig->setLoader( $loader );
		}
		
		public function render( $filename, $variables = [] ) {
			$template = $this->environment->twig->load( $filename );
			
			// We need to replace variables here!
			$content = $template->render( $variables );
			
			// Debugging
			file_put_contents( $this->storage_path . time() .'.html', $content );
			
			return $content;
		}
	}
