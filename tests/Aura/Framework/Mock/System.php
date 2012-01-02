<?php
namespace Aura\Framework\Mock;
use Aura\Framework\System as RealSystem;
class System extends RealSystem
{
    static public function newInstance()
    {
        $root = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'tmp';
        $system = new self($root);
        return $system;
    }
    
    public function create()
    {
        $dir = $this->getRootPath();
        
        if (is_dir($dir)) {
            $this->remove();
        }
        
        if (! is_dir($dir)) {
            mkdir($dir);
        }
        
        mkdir($this->getConfigPath());
        mkdir($this->getIncludePath());
        mkdir($this->getPackagePath());
        mkdir($this->getTmpPath());
        mkdir($this->getWebPath());
    }
    
    public function remove()
    {
        $dir = $this->getRootPath();
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $path) {
            if ($path->isDir()) {
                rmdir($path->__toString());
            } else {
                unlink($path->__toString());
            }
        }
        rmdir($dir);
    }
}
