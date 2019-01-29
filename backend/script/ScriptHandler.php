<?php
namespace Dgfactor\Script;

use Backend\BaseBundle\CM4\CM4WidgetInterface;
use Composer\Script\PackageEvent;
use Dgfactor\Script\Manipulator\BundleManipulator;
use Dgfactor\Script\Manipulator\KernelManipulator;
use Nette\Reflection\AnnotationsParser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;

class ScriptHandler
{
    protected static $installedPackagesPath = array();
    protected static $appPath;

    protected static function findInstallationPath($composer)
    {
        $packages = $composer->getRepositoryManager()->getLocalRepository()->getPackages();
        $installationManager = $composer->getInstallationManager();
        foreach($packages as $package){
            $originDir = $installationManager->getInstallPath($package);
            self::$installedPackagesPath[$originDir] = true;
        }
    }

    public static function scanPath(Event $event)
    {
        self::updateKernelPath($event->getComposer()->getConfig());
        self::findInstallationPath($event->getComposer());

        foreach (self::$installedPackagesPath as $path => $value){
            $finder = new Finder();
            foreach($finder->files()->in($path)->depth('==0')->name('*Bundle.php') as $file){
                self::getClass($file, $event->getIO());
            }
        }
    }

    protected static function updateKernelPath($config)
    {
        self::$appPath = realpath($config->get('vendor-dir').'/../app');
    }

    protected static function getClass(\SplFileInfo $file, IOInterface $io)
    {
        $manipulator = new BundleManipulator($file);
        $parser = $manipulator->parse();
        $fullClass = $parser->getFullClass();
        $class = $parser->getClass();

        if(self::isCM4Widget($fullClass)){
            self::writeKernel($fullClass, $io);
            self::writeRouting($file->getPath(), $class, $io);
        }
    }

    protected static function isCM4Widget($class)
    {
        try {
            @$implements = class_implements($class);
            return in_array('Backend\\BaseBundle\\CM4\\CM4WidgetInterface', $implements);
        } catch (\ErrorException $e){
            return false;
        }
    }

    protected static function writeRouting($bundleBasepath, $class, IOInterface $io)
    {
        $routingFile = realpath(self::$appPath.'/config/routing.yml');
        $distRoutingFile = realpath($bundleBasepath.'/Resources/cm4/routing.yml');

        if(!$routingFile){
            return;
        }

        $routingConfig = file_get_contents($routingFile);

        if(preg_match('/\\s*#\\s*('.$class.'):\\s*/si', $routingConfig, $match)){
            return;
        }

        $yaml = Yaml::parse($routingConfig);

        if(isset($yaml[$class])){
            return;
        }

        if(!$distRoutingFile || !file_exists($distRoutingFile)){
            return;
        }

        $file = new \SplFileObject($routingFile, "a+");
        $file->fwrite(file_get_contents($distRoutingFile));
        $file->fwrite("\n");
        $io->write("<info>Enable Routing: $class</info>");
    }

    protected static function writeKernel($class, IOInterface $io)
    {
        $kernelFile = realpath(self::$appPath.'/AppKernel.php');
        if(!$kernelFile){
            return;
        }
        $manipulator = new KernelManipulator(new \SplFileInfo($kernelFile));
        if($manipulator->parse()->addBundle($class)){
            $io->write("<info>Enable Bundle: $class</info>");
        }
    }
}