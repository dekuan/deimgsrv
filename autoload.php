<?php

function deimgClassLoader($class)
{
	if ( 0 === strpos(  $class, 'dekuan\deimgsrv\\' ) )
	{
		$sClassName = str_replace( 'dekuan\deimgsrv\\', '', $class );

		$sClassName = str_replace( '\\', DIRECTORY_SEPARATOR, $sClassName );
		$file = __DIR__ . DIRECTORY_SEPARATOR .'src'. DIRECTORY_SEPARATOR . $sClassName . '.php';
		if (file_exists($file)) {
			require_once $file;
		}
	}
}
spl_autoload_register('deimgClassLoader');