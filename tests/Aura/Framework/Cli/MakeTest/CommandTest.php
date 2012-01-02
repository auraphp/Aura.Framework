<?php
namespace Aura\Framework\Cli\MakeTest;
use Aura\Framework\Cli\AbstractCommandTest;
use Aura\Framework\Inflect;

/**
 * Test class for make_test\Command.
 */
class CommandTest extends AbstractCommandTest
{
    protected $command_name = 'MakeTest';
    
    protected $inflect;
    
    protected function newCommand($argv = [])
    {
        $command = parent::newCommand($argv);
        $this->inflect = new Inflect;
        $command->setSystem($this->system);
        $command->setInflect($this->inflect);
        return $command;
    }
    
    /**
     * @expectedException Aura\Framework\Exception\SourceNotFound
     */
    public function test_sourceNotFound()
    {
        $command = $this->newCommand(['package/Aura.Framework/src/NoSuchClass.php']);
        $command->exec();
    }
    
    public function testTargetFileExists()
    {
        $src_file = $this->system->getPackagePath('Vendor.Package/src/Vendor/Package/Classname.php');
        mkdir(dirname($src_file), 0777, true);
        $src_text = "<?php namespace Vendor\Package; class Classname {}";
        file_put_contents($src_file, $src_text);
        
        $test_file = $this->system->getPackagePath('Vendor.Package/tests/Vendor/Package/ClassnameTest.php');
        mkdir(dirname($test_file), 0777, true);
        $test_text = "<?php namespace Vendor\Package; class ClassnameTest {}";
        file_put_contents($test_file, $test_text);
        
        $this->setExpectedException('Aura\Framework\Exception\TestFileExists');
        $command = $this->newCommand([$src_file]);
        $command->exec();
    }
    
    /**
     * @todo check the resulting file contents, not just that it exists
     */
    public function test()
    {
        // write out a fake class in a fake package
        $vendor  = 'MockVendor';
        $package = 'MockPackage';
        $class   = 'MockClass';
        
        $system_dir = $this->system->getRootPath();
        
        $package_dir  = $this->system->getPackagePath();
        
        $incl_file = "{$package_dir}/{$vendor}.{$package}/src/{$vendor}/{$package}/{$class}.php";
        $test_file = "{$package_dir}/{$vendor}.{$package}/tests/{$vendor}/{$package}/{$class}Test.php";
        
        @unlink($incl_file);
        @unlink($test_file);
        
        @mkdir(dirname($incl_file), 0777, true);
        @mkdir(dirname($test_file), 0777, true);
        
        $code = "<?php
namespace {$vendor}\\{$package};
class {$class} {}
";
        // write directly to the include dir instead of to a src dir and then
        // symlinking, to simplify things for the test
        file_put_contents($incl_file, $code);
        
        // make a test from the fake class
        $command = $this->newCommand(
            ["$package_dir/{$vendor}.{$package}/src/{$vendor}/{$package}/{$class}.php"],
            $system_dir
        );
        
        // needs to be in the include-path
        include_once $incl_file;
        $command->exec();
        
        // find the output file
        $this->assertFileExists($test_file);
        
        // delete the fake class and the fake output file
        unlink($incl_file);
        unlink($test_file);
    }
}
