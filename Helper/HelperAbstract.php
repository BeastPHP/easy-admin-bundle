<?php

namespace Beast\EasyAdminBundle\Helper;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * description content
 *
 * @abstract
 * @access    public
 * @author    Guz
 */
class HelperAbstract
{
    protected static $class;

    /**
     * @var KernelInterface
     */
    protected static $kernel;

    public function __construct()
    {
        global $kernel;

        if ($kernel == null) {
            static::$kernel = static::createKernel();
            if (!isset($_SERVER['REMOTE_ADDR'])) {
                $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            }
            static::$kernel->boot();
        } else {
            static::$kernel = $kernel;
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return static::$kernel->getContainer();
    }


    /**
     * @return KernelInterface
     */
    public function getKernel()
    {
        return static::$kernel;
    }

    /**
     * @return string app root dir
     */
    public function getRootDir()
    {
        return static::$kernel->getRootDir();
    }

    public function getLogDir()
    {
        return static::$kernel->getLogDir();
    }

    /**
     * Finds the directory where the phpunit.xml(.dist) is stored.
     *
     * If you run tests with the PHPUnit CLI tool, everything will work as expected.
     * If not, override this method in your test classes.
     *
     * @return string The directory where phpunit.xml(.dist) is stored
     *
     * @throws \RuntimeException
     */
    protected static function getPhpUnitXmlDir()
    {
        if (!isset($_SERVER['argv']) || false === strpos($_SERVER['argv'][0], 'phpunit')) {
            throw new \RuntimeException('You must override the WebTestCase::createKernel() method.');
        }

        $dir = static::getPhpUnitCliConfigArgument();
        if (null === $dir &&
            (is_file(getcwd() . DIRECTORY_SEPARATOR . 'phpunit.xml') ||
                is_file(getcwd() . DIRECTORY_SEPARATOR . 'phpunit.xml.dist'))) {
            $dir = getcwd();
        }

        // Can't continue
        if (null === $dir) {
            throw new \RuntimeException('Unable to guess the Kernel directory.');
        }

        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        return $dir;
    }

    /**
     * Finds the value of the CLI configuration option.
     *
     * PHPUnit will use the last configuration argument on the command line, so this only returns
     * the last configuration argument.
     *
     * @return string The value of the PHPUnit CLI configuration option
     */
    private static function getPhpUnitCliConfigArgument()
    {
        $dir = null;
        $reversedArgs = array_reverse($_SERVER['argv']);
        foreach ($reversedArgs as $argIndex => $testArg) {
            if (preg_match('/^-[^ \-]*c$/', $testArg) || $testArg === '--configuration') {
                $dir = realpath($reversedArgs[$argIndex - 1]);
                break;
            } elseif (strpos($testArg, '--configuration=') === 0) {
                $argPath = substr($testArg, strlen('--configuration='));
                $dir = realpath($argPath);
                break;
            }
        }

        return $dir;
    }

    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @return string The Kernel class name
     *
     * @throws \RuntimeException
     */
    protected static function getKernelClass()
    {
        $dir = isset($_SERVER['KERNEL_DIR']) ? $_SERVER['KERNEL_DIR'] : static::getPhpUnitXmlDir();

        $finder = new Finder();
        $finder->name('*Kernel.php')->depth(0)->in($dir);
        $results = iterator_to_array($finder);
        if (!count($results)) {
            throw new \RuntimeException(
                'Either set KERNEL_DIR in your phpunit.xml according to 
                http://symfony.com/doc/current/book/testing.html#your-first-functional-test or override the 
                WebTestCase::createKernel() method.'
            );
        }

        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }

    /**
     * Creates a Kernel.
     *
     * Available options:
     *
     *  * environment
     *  * debug
     *
     * @param array $options An array of options
     *
     * @return KernelInterface A KernelInterface instance
     */
    protected static function createKernel(array $options = array())
    {
        if (null === static::$class) {
            static::$class = static::getKernelClass();
        }

        return new static::$class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }
}
