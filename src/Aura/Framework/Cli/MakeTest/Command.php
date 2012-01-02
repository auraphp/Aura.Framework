<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @package Aura.Framework
 * 
 */
namespace Aura\Framework\Cli\MakeTest;
use Aura\Framework\Cli\Command as CliCommand;
use Aura\Framework\System;
use Aura\Framework\Inflect;
use Aura\Framework\Exception\SourceNotFound;
use Aura\Framework\Exception\TestFileExists;

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
 */
class Command extends CliCommand
{
    /**
     * 
     * The include path before being modified by this class.
     * 
     * @var string
     * 
     */
    protected $include_path;
    
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
     * The directory where packages reside.
     * 
     * @var string
     * 
     */
    protected $package_dir;
    
    /**
     * 
     * The directory where test classes should be created.
     * 
     * @var string
     * 
     */
    protected $test_dir;
    
    /**
     * 
     * Sets a System object for this class.
     * 
     * @param System
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
     * @param System
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
     * Runs before `action()` as called by signal. Modifies the include-path
     * so that PHPUnit is part of it.
     * 
     * @return void
     * 
     */
    public function preAction()
    {
        $this->include_path = ini_get('include_path');
        $dir = dirname(dirname(dirname(dirname(dirname(__DIR__)))))
             . DIRECTORY_SEPARATOR . 'pear' . DIRECTORY_SEPARATOR . 'php';
        ini_set('include_path', $this->include_path . PATH_SEPARATOR . $dir);
    }
    
    /**
     * 
     * Runs after `action()` as called by signal. Restores the include-path.
     * 
     * @return void
     * 
     */
    public function postAction()
    {
        ini_set('include_path', $this->include_path);
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
        // get the class file for the test source
        $spec = $this->params[0];
        
        // split up the pieces of the class file specification
        list($vendor, $package, $class) = $this->getVendorPackageClass($spec);
        
        // the fully-qualified class to write a test from
        $incl_name = $class;
        
        // the *class name only* of the test to write
        $test_name = "{$class}Test";
        
        // the original source file
        $incl_file = $spec;
        $this->stdio->outln("Source file is '$incl_file'.");
        
        // look where the test file will go:
        // package/$vendor.$package/tests/$class_to_file
        $test_file = $this->system->getPackagePath(
            "{$vendor}.{$package}/tests/" . $this->inflect->classToFile($test_name)
        );
        
        // does the test file exist already?
        if (is_file($test_file)) {
            throw new TestFileExists($test_file);
        }
        
        // generate the test skeleton code
        $skel = new \PHPUnit_Util_Skeleton_Test(
            $incl_name,
            $incl_file,
            $test_name,
            $test_file
        );
        $skel_code = $skel->generate();
        
        // modify the resulting code
        $test_code = $this->modifySkeleton($skel_code, "$vendor\\$package");
        
        // make sure a directory exists for the test file
        $test_dir = dirname($test_file);
        @mkdir($test_dir, 0755, true);
        
        // write the test file
        file_put_contents($test_file, $test_code);
        $this->stdio->outln("Test file created at '$test_file'.");
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
    protected function getVendorPackageClass($spec)
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
        
        // turn the rest into a class, minus .php
        $class = substr(implode('\\', $part), 0, -4);
        
        return [$vendor, $package, $class];
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
    protected function modifySkeleton($skel, $namespace)
    {
        $skel = preg_replace('/\nrequire_once.*\n/', '', $skel);
        
        $skel = str_replace(
            'extends PHPUnit_Framework_TestCase',
            'extends \PHPUnit_Framework_TestCase',
            $skel
        );
        
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
