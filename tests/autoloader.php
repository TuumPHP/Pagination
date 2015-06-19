<?php
if( defined( 'VENDOR_DIRECTORY' ) ) {
    return;
}
elseif( file_exists( __DIR__ . '/../vendor/autoload.php' ) ) {
    /** @noinspection PhpIncludeInspection */
    require_once __DIR__ . '/../vendor/autoload.php';
    define( 'VENDOR_DIRECTORY', __DIR__ . '/../vendor/' );
}
elseif( file_exists( __DIR__ . '/../../../../vendor/autoload.php' ) ) {
    /** @noinspection PhpIncludeInspection */
    require_once __DIR__ . '/../../../../vendor/autoload.php';
    define( 'VENDOR_DIRECTORY', __DIR__ . '/../../../../vendor/' );
}
else {
    die( 'vendor directory not found' );
}

$loader = new \Composer\Autoload\ClassLoader();

$loader->addPsr4( 'tests\\',  __DIR__ );
$loader->register();

