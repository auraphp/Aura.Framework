<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Web\Asset;
use Aura\Framework\Web\AbstractPage;

/**
 * 
 * Provides a public interface to web assets for a package.
 * 
 * Your package should be set up like this:
 * 
 *      Vendor.Package/
 *          config/
 *          scripts/
 *          src/
 *          tests/
 *          web/
 *              images/
 *              scripts/
 *              styles/
 *                  foo.css
 * 
 * You can then use the URL `/asset/Vendor.Package/styles/foo.css` to access
 * the package asset, even though it's not in the web document root.
 * 
 * Additionally, you can cache the assets to the web document root, so that
 * they are served statically instead of through PHP.
 * 
 * @package Aura.Framework
 * 
 */
class Page extends AbstractPage
{
    /**
     * 
     * The Aura config modes in which we should cache web assets.
     * 
     * @var array
     * 
     */
    protected $cache_config_modes = [];
    
    /**
     * 
     * The subdirectory inside the web document root where we should cache
     * web assets.
     * 
     * @var array
     * 
     */
    protected $web_cache_dir;
    
    /**
     * 
     * Sets the config modes in which caching should take place.
     * 
     * @param array $modes An array of mode names.
     * 
     * @return void
     * 
     */
    public function setCacheConfigModes(array $modes = [])
    {
        $this->cache_config_modes = $modes;
    }
    
    /**
     * 
     * Sets the subdirectory in the web document root where web assets should
     * be cached.
     * 
     * @param string $dir
     * 
     * @return void
     * 
     */
    public function setWebCacheDir($dir)
    {
        $this->web_cache_dir = $dir;
    }
    
    /**
     * 
     * Given a package name and an asset file name, delivers the asset
     * (and caches it if the config mode is correct).
     * 
     * @param string $package The package name (e.g., `Vendor.Package`).
     * 
     * @param string $file The asset file name (e.g. `images/logo.jpg`).
     * 
     * @return void
     * 
     */
    public function actionIndex($package = null, $file = null)
    {
        // add the format to the filename
        $file .= $this->format;
        
        // get the real path to the asset
        $fakepath = $this->system->getPackagePath("$package/web/$file");
        $realpath = realpath($fakepath);
        
        // does the asset file exist?
        if (! file_exists($realpath) || ! is_readable($realpath)) {
            $content = "Asset not found: "
                     . htmlspecialchars($fakepath, ENT_QUOTES, 'UTF-8');
            $this->response->setStatusCode(404);
            $this->response->setContent($content);
            return;
        }
        
        // are we in a config mode that wants us to cache?
        $config_mode = $this->context->getEnv('AURA_CONFIG_MODE', 'default');
        if (in_array($config_mode, $this->cache_config_modes)) {
            // copy source to this target cache location
            $path = $this->web_cache_dir . DIRECTORY_SEPARATOR
                  . $package . DIRECTORY_SEPARATOR
                  . $file;

            $webcache = $this->system->getWebPath($path);
        
            // make sure we have a dir for it
            $dir = dirname($webcache);
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            
            // copy from the source package to the target cache dir for the 
            // next time this package asset is requested
            copy($realpath, $webcache);
        }
        
        // open the asset file using a shared (read) lock
        $fh = fopen($realpath, 'rb');
        flock($fh, LOCK_SH);
        
        // set the response content to the file handle
        $this->response->setContent($fh);
    }
}
