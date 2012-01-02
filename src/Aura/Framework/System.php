<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework;

/**
 * 
 * Keeps track of the Aura system directories.
 * 
 * @package Aura.Framework
 * 
 */
class System
{
    /**
     * 
     * The root directory of the Aura system.
     * 
     * @var string
     * 
     */
    protected $root;
    
    /**
     * 
     * Constructor.
     * 
     * @param string $root The root directory of the Aura system.
     * 
     */
    public function __construct($root)
    {
        $this->root = $root;
    }
    
    /**
     * 
     * Gets the path for any directory, along with an optional subdirectory
     * path.
     * 
     * @param string $dir The directory name to get the path for.
     * 
     * @param string $sub An optional subdirectory path.
     * 
     * @return The full directory path, with proper directory separators.
     * 
     */
    protected function getSubPath($dir, $sub = null)
    {
        $path = $this->root . DIRECTORY_SEPARATOR . $dir;
        if ($sub) {
            $path .= DIRECTORY_SEPARATOR
                   . str_replace('/', DIRECTORY_SEPARATOR, $sub);
        }
        return $path;
    }
    
    /**
     * 
     * Gets the root path for the Aura system, along with an optional 
     * subdirectory path.
     * 
     * @param string $sub An optional subdirectory path.
     * 
     * @return The full directory path, with proper directory separators.
     * 
     */
    public function getRootPath($sub = null)
    {
        $path = $this->root;
        if ($sub) {
            $path .= DIRECTORY_SEPARATOR
                   . str_replace('/', DIRECTORY_SEPARATOR, $sub);
        }
        return $path;
    }
    
    /**
     * 
     * Gets the package path for the Aura system, along with an optional 
     * subdirectory path.
     * 
     * @param string $sub An optional subdirectory path.
     * 
     * @return The full directory path, with proper directory separators.
     * 
     */
    public function getPackagePath($sub = null)
    {
        return $this->getSubPath('package', $sub);
    }
    
    /**
     * 
     * Gets the tmp path for the Aura system, along with an optional 
     * subdirectory path.
     * 
     * @param string $sub An optional subdirectory path.
     * 
     * @return The full directory path, with proper directory separators.
     * 
     */
    public function getTmpPath($sub = null)
    {
        return $this->getSubPath('tmp', $sub);
    }
    
    /**
     * 
     * Gets the config path for the Aura system, along with an optional 
     * subdirectory path.
     * 
     * @param string $sub An optional subdirectory path.
     * 
     * @return The full directory path, with proper directory separators.
     * 
     */
    public function getConfigPath($sub = null)
    {
        return $this->getSubPath('config', $sub);
    }
    
    /**
     * 
     * Gets the include path for the Aura system, along with an optional 
     * subdirectory path.
     * 
     * @param string $sub An optional subdirectory path.
     * 
     * @return The full directory path, with proper directory separators.
     * 
     */
    public function getIncludePath($sub = null)
    {
        return $this->getSubPath('include', $sub);
    }
    
    public function getWebPath($sub = null)
    {
        return $this->getSubPath('web', $sub);
    }
}
