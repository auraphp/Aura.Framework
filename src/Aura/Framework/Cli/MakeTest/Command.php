<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework\Cli\MakeTest;
use Aura\Framework\Cli\AbstractCommand;
use Aura\Framework\System;
use Aura\Framework\Inflect;
use Aura\Framework\Exception\SourceNotFound;
use Aura\Framework\Exception\TestFileExists;
use Aura\Framework\Exception\TestFileNotCreated;
use Aura\Framework\Exception\TestFileNotMoved;

/**
 * 
 * This command uses PHPUnit to make a skeleton test file from an existing 
 * class.
 * 
 * Usage is ...
 * 
 *      $ php package/Aura.Framework/cli/make-test {$FILE}
 * 
 * ... where `$FILE` is a package file path, e.g. 
 * `package/Aura.Framework/System.php`.
 * 
 * @package Aura.Framework
 * 
 * @todo Include path/autoloader is not honored by the phpunit test creator.
 * 
 */
class Command extends AbstractCommand
{
    /**
     * 
     * A word inflector.
     * 
     * @var Inflect
     * 
     */
    protected $inflect;
    
    /**
     * 
     * A system object.
     * 
     * @var System
     * 
     */
    protected $system;
    
    protected $phpunit;
    
    protected $bootstrap;
    
    /**
     * 
     * Sets a System object for this class.
     * 
     * @param System $system The System object.
     * 
     * @return void
     * 
     */
    public function setSystem(System $system)
    {
        $this->system = $system;
    }
    
    /**
     * 
     * Sets an Inflect object for this class.
     * 
     * @param Inflect $inflect An Inflect object.
     * 
     * @return void
     * 
     */
    public function setInflect(Inflect $inflect)
    {
        $this->inflect = $inflect;
    }
    
    /**
     * 
     * Sets the phpunit executable.
     * 
     * @param string $phpunit The phpunit executable path.
     * 
     * @return void
     * 
     */
    public function setPhpunit($phpunit)
    {
        $this->phpunit = $phpunit;
    }
    
    /**
     * 
     * Sets the location of the bootstrap file.
     * 
     * @param string $bootstrap The path to the bootstrap file.
     * 
     */
    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }
    
    /**
     * 
     * Creates a test file from an existing class.
     * 
     * @return void
     * 
     */
    public function action()
    {
        // get the file for the source to be tested
        $source_file = $this->params[0];
        
        // what test file will phpunit create?
        $create_file = str_replace('.php', 'Test.php', $source_file);
        if (is_readable($create_file)) {
            // don't want to overwrite an existing file
            throw new TestFileExists($create_file);
        }
        
        // what target file will we move it to?
        $target_file = str_replace(
            DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR,
            $create_file
        );
        if (is_readable($target_file)) {
            // don't want to overwrite an existing file
            throw new TestFileExists($target_file);
        }
        
        // get the class name
        $class = $this->getClass($source_file);
        
        // create the phpunit command
        $cmd = $this->phpunit;
        if ($this->bootstrap) {
            $cmd .= " --bootstrap {$this->bootstrap}";
        }
        $cmd .= " --skeleton-test '{$class}' {$source_file}";
        
        exec($cmd);
        
        // did it get created?
        if (! is_readable($create_file)) {
            throw new TestFileNotCreated("Not created: '{$create_file}'");
        }
        
        // make sure we have a directory for the new location
        @mkdir(dirname($target_file), 0755, true);
        
        // move it to the proper location
        $ok = rename($create_file, $target_file);
        if (! $ok) {
            throw new TestFileNotMoved("Could not move from '{$create_file}' to '{$target_file}'");
        }
        
        // modify it in place
        $skel = file_get_contents($target_file);
        $skel = $this->modifySkeleton($skel);
        file_put_contents($target_file, $skel);
        
        // done!
        $this->stdio->outln("Test file created at '{$target_file}'.");
    }
    
    /**
     * 
     * Given a class specification, extract the vendor, package, and class 
     * names.
     * 
     * @param string $spec The fully-qualified class specification.
     * 
     * @return array A seqential array of vendor name, package name, and
     * class name.
     * 
     */
    protected function getClass($spec)
    {
        // incoming spec: package/Vendor.Package/src/Vendor/Package/Class.php
        $real = realpath($spec);
        if (! $real) {
            throw new SourceNotFound($spec);
        }
        
        // strip off the "package/" dir prefix
        $len  = strlen($this->system->getPackagePath() . DIRECTORY_SEPARATOR);
        $spec = substr($real, $len);
        
        // this should leave us with, e.g., Vendor.Package/src/Vendor/Package/Class.php
        // get the package name out
        $part = explode(DIRECTORY_SEPARATOR, $spec);
        
        // pull off the top part and turn into vendor and package
        list($vendor, $package) = explode('.', array_shift($part));
        
        // pull off 'src'
        array_shift($part);
        
        // turn the rest into the full class name, minus .php
        $class = substr(implode('\\', $part), 0, -4);
        
        return $class;
    }
    
    /**
     * 
     * Given a test class skeleton from PHPUnit, modify it so that it works
     * nicely within the Aura testing system.
     * 
     * @param string $skel The PHPUnit test class skeleton.
     * 
     * @param string $namespace The namespace of the class being tested.
     * 
     * @return string The modified test skeleton.
     * 
     */
    protected function modifySkeleton($skel)
    {
        $skel = preg_replace('/\n\nrequire_once.*\n/', '', $skel);
        
        $skel = str_replace(
            "function setUp()\n    {",
            "function setUp()\n    {\n        parent::setUp();",
            $skel
        );
        
        $skel = str_replace(
            "function tearDown()\n    {",
            "function tearDown()\n    {\n        parent::tearDown();",
            $skel
        );
        
        $skel = preg_replace('/\?\>\n$/', '', $skel);
        
        return $skel;
    }
}
