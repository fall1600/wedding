<?php
namespace Backend\BaseBundle\Command;

use Backend\BaseBundle\Event\DynamicCustomConfigEvent;
use Backend\BaseBundle\Service\BackendConfigBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class AssetsInstallCommand extends ContainerAwareCommand
{
    const METHOD_COPY = 'copy';
    const METHOD_ABSOLUTE_SYMLINK = 'absolute symlink';
    const METHOD_RELATIVE_SYMLINK = 'relative symlink';

    protected $bundleConfig = array();

    protected $buildBundles = array();
    /** @var  EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dgfactor:assets:install')
            ->setDefinition(array(
                new InputArgument('target', InputArgument::OPTIONAL, '後台前端樣板目錄', 'vue'),
            ))
            ->setDescription('將 bundle 的 vue 樣板安裝到 vue 專案目錄底下')
            ->setHelp(<<<'EOT'
將 bundle 的 vue 樣板安裝到 vue 專案目錄底下

  <info>php %command.full_name% vue</info>

EOT
            )
        ;
    }

    protected function dumpTranslation(SymfonyStyle $io, $bundlesDir)
    {
        $target = "$bundlesDir/translations";
        $io->text('Trying to dump translations.');
        $container = $this->getContainer();
        foreach($container->getParameter('support_locales') as $locale) {
            $cate = $container->get('translator')->getCatalogue($locale);
            $container->get('translation.writer')->writeTranslations($cate, 'json', array('path' => $target, 'json_encoding' => JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        file_put_contents("$target/config.json", json_encode(array(
            'locales' => $container->getParameter('support_locales'),
            'fallback' => $container->getParameter('fallback_locale')
        )));
    }
    protected function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    protected function addConfigPath($bundleBaseName, $configPath)
    {
        $this->bundleConfig[$bundleBaseName] = $configPath;
    }

    protected function fixRelativeComponentPath($bundleName, $configs)
    {
        return $configs;
        $fixedConfigs = array();
        foreach($configs as $routeName => $config){
            if(isset($config['component'])){
                $config['component'] = function() use($bundleName, $config){
                    return "require \"{$config['component']}\"";
                };
                $fixedConfigs[$routeName] = $config;
            }
        }
        return $fixedConfigs;
    }

    protected function convertToJS($config)
    {
        $js = json_encode($config, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);
        $js = preg_replace('/\"component\":\s*\"(.*?)\"/i', '"component": require("$1")', $js);
        return $js;
    }

    protected function generateRouteJsonConfig(SymfonyStyle $io, $bundlesDir)
    {
        $routes = array();
        foreach ($this->bundleConfig as $bundleName => $configPath){
            if(!file_exists($configPath."/router.json")){
                continue;
            }
            $routeConfig = json_decode(file_get_contents($configPath."/router.json"), true);
            if(!is_array($routeConfig) || count($routeConfig) == 0){
                continue;
            }
            $routes[$bundleName] = $routeConfig;
        }
        file_put_contents("$bundlesDir/routers.json", json_encode($routes, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }

    protected function generateRouteCoffeeConfig(SymfonyStyle $io, $bundlesDir)
    {
        $config = array();
        $routes = json_decode(file_get_contents("$bundlesDir/routers.json"), true);
        foreach($routes as $bundleName => $routeConfig){
            foreach($routeConfig as $route){
                if(!is_array($route) || count($route) == 0){
                    continue;
                }
                $config[] = $route;
            }
        }
        $js = $this->convertToJS($config);
        file_put_contents("$bundlesDir/routers.coffee", "module.exports = \n$js");
        unlink("$bundlesDir/routers.json");
    }

    protected function generateMenuConfig(SymfonyStyle $io, $bundlesDir)
    {
        $config = array();
        foreach ($this->bundleConfig as $bundleName => $configPath){
            $jsonConfig = json_decode(file_get_contents($configPath."/menu.json"), true);
            $config = $this->deepArrayKeySort($a = $this->numericArrayMerge($config, $jsonConfig));
        }
        $config = "module.exports = ".json_encode($config, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        file_put_contents("$bundlesDir/menu.coffee", $config);
    }

    protected function deepArrayKeySort($array)
    {
        $numeric = array();
        $assoc = array();

        foreach ($array as $index => $value){
            if(is_array($value)){
                $value = $this->deepArrayKeySort($value);
            }
            if(is_numeric($index)){
                $numeric[$index] = $value;
            }
            else{
                $assoc[$index] = $value;
            }
        }
        krsort($numeric);
        krsort($assoc);
        return array_merge(array_values($numeric), $assoc);
    }

    protected function numericArrayMerge($origin, $new)
    {
        foreach ($new as $index => $item){
            if(!isset($origin[$index])){
                $origin[$index] = $item;
                continue;
            }

            if(is_array($origin[$index])) {
                if(is_array($item)) {
                    $origin[$index] = $this->numericArrayMerge($origin[$index], $item);
                    continue;
                }
                $origin[$index][] = $item;
                continue;
            }

            $tmp = $origin[$index];
            if(is_array($item)){
                $origin[$index] = $item;
                $origin[$index][] = $tmp;
                continue;
            }

            $origin[$index] = array($tmp, $item);
        }
        return $origin;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->filesystem = $this->getContainer()->get('filesystem');
        $this->eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $targetArg = rtrim($input->getArgument('target'), '/');
        $this->linkToBackend($targetArg);

        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        // Create the bundles directory otherwise symlink will fail.
        $bundlesDir = $targetArg.'/cm4';
        $this->filesystem->mkdir($bundlesDir);
        $io = $this->createSymfonyStyleIO($input, $output);

        if($this->writeBundles($output, $bundlesDir, $io)) {
            $io->error('Some errors occurred while installing assets.');
            return 1;
        }

        $this->generateRouteJsonConfig($io, $bundlesDir);
        $this->generateMenuConfig($io, $bundlesDir);
        $this->dumpTranslation($io, $bundlesDir);
        $io->success('All assets were successfully installed.');

        $this->dumpAllBundleJson($io);
        $this->dumpMenuJson($io);
        $io->success('All JSON config were successfully dump.');

        $this->buildBundles();
        $io->success('All Custom config were successfully dump.');

        $this->generateRouteCoffeeConfig($io, $bundlesDir);
    }

    protected function linkToBackend($targetArg)
    {
        $cm4CustomPath = realpath($this->getContainer()->get('kernel')->getRootDir()."/../vue.cm4");
        $backendBaseBundlePath = $this->getContainer()->get('kernel')->getBundle('BackendBaseBundle')->getPath();
        $this->symlink("$backendBaseBundlePath/Resources/vue/backend", $targetArg);
        if($cm4CustomPath !== false){
            $this->symlink($cm4CustomPath, "$targetArg/src/cm4");
        }
    }

    /**
     * Creates symbolic link.
     *
     * @param string $originDir
     * @param string $targetDir
     * @param bool   $relative
     *
     * @throws IOException If link can not be created.
     */
    private function symlink($originDir, $targetDir)
    {
        $originDir = $this->filesystem->makePathRelative($originDir, realpath(dirname($targetDir)));
        $this->filesystem->symlink($originDir, $targetDir);
        if (!file_exists($targetDir)) {
            throw new IOException(sprintf('Symbolic link "%s" was created but appears to be broken.', $targetDir), 0, null, $targetDir);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return SymfonyStyle
     */
    protected function createSymfonyStyleIO(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->newLine();

        $io->text('Trying to install assets as <info>relative symbolic links</info>.');

        $io->newLine();
        return $io;
    }

    /**
     * @param OutputInterface $output
     * @param $bundlesDir
     * @param $io
     * @return int
     */
    protected function writeBundles(OutputInterface $output, $bundlesDir, $io)
    {
        $rows = array();
        $exitCode = 0;
        $bundleTypes = array(
            'components',
            'assets',
            'static',
        );

        /** @var BundleInterface $bundle */
        foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
            $baseDir = "{$bundle->getPath()}/Resources/vue";
            $useSymLink = $this->isUseSymlink($baseDir);
            $bundleBaseName = preg_replace('/bundle$/', '', strtolower($bundle->getName()));
            foreach ($bundleTypes as $type) {
                if (!$this->filesystem->exists($originDir = "$baseDir/$type")) {
                    continue;
                }
                $this->addConfigPath($bundleBaseName, realpath($originDir . '/../'));
                $targetDir = "$bundlesDir/$type/$bundleBaseName";
                if (!$this->filesystem->exists("$bundlesDir/$type")) {
                    $this->filesystem->mkdir("$bundlesDir/$type");
                }
                if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                    $message = sprintf("%s\n-> %s", $bundle->getName(), $targetDir);
                } else {
                    $message = "{$bundle->getName()}/$type";
                }

                try {
                    $this->filesystem->remove($targetDir);
                    if($useSymLink) {
                        $this->symlink($originDir, $targetDir);
                        $rows[] = array(sprintf('<fg=green;options=bold>%s</>', '\\' === DIRECTORY_SEPARATOR ? 'OK' : "\xE2\x9C\x94" /* HEAVY CHECK MARK (U+2714) */), $message, self::METHOD_RELATIVE_SYMLINK);
                    }
                    else{
                        $this->hardCopy($originDir, $targetDir);
                        $rows[] = array(sprintf('<fg=green;options=bold>%s</>', '\\' === DIRECTORY_SEPARATOR ? 'OK' : "\xE2\x9C\x94" /* HEAVY CHECK MARK (U+2714) */), $message, self::METHOD_COPY);
                        $this->notifyDynamicCustomConfig($bundle, $targetDir);
                    }
                } catch (\Exception $e) {
                    $exitCode = 1;
                    $rows[] = array(sprintf('<fg=red;options=bold>%s</>', '\\' === DIRECTORY_SEPARATOR ? 'ERROR' : "\xE2\x9C\x98" /* HEAVY BALLOT X (U+2718) */), $message, $e->getMessage());
                }
            }
            if(!$useSymLink) {
                $this->buildBundles[] = array(
                    'bundleBaseName' => $bundleBaseName,
                    'targetDir' => $targetDir,
                    'baseDir' => $baseDir
                );
            }
        }

        $io->table(array('', 'Bundle', 'Method / Error'), $rows);
        return $exitCode;
    }

    protected function buildBundles()
    {
        foreach ($this->buildBundles as $buildBundle) {
            $this->getContainer()->get('backend_base.backend.config.builder')->build($buildBundle['bundleBaseName'], $buildBundle['targetDir'], $buildBundle['baseDir']);
        }
    }
    protected function isUseSymlink($originDir)
    {
        if(is_dir("$originDir/config")){
            return Finder::create()->name("*.json")->in("$originDir/config")->count() == 0;
        }

        return true;
    }

    /**
     * Copies origin to target.
     *
     * @param string $originDir
     * @param string $targetDir
     *
     * @return string
     */
    private function hardCopy($originDir, $targetDir)
    {
        $this->filesystem->mkdir($targetDir, 0777);
        // We use a custom iterator to ignore VCS files
        $this->filesystem->mirror($originDir, $targetDir, Finder::create()->ignoreDotFiles(false)->in($originDir));

        return self::METHOD_COPY;
    }

    protected function dumpMenuJson(SymfonyStyle $io)
    {
        $config = array();
        // 拿到所有menu JSON
        foreach ($this->bundleConfig as $bundleName => $configPath){
            $jsonConfig = json_decode(file_get_contents($configPath."/menu.json"), true);
            $config = $this->deepArrayKeySort($a = $this->numericArrayMerge($config, $jsonConfig));
        }

        // 拿到 menu name
        $menuKeys = array();
        foreach ($config as $menuKey => $menu){
            $menuKeys[] = $menuKey;
        }
        $jsonMenuKeys = array();
        foreach ($menuKeys as $menuKey){
            if($config[$menuKey]['route'] && is_array($config[$menuKey]['route']) ){
                foreach($config[$menuKey]['route'] as $key => $menu){
                    $jsonMenuKeys[] = $key;
                }
            }
        }

        // 組成 menu json
        $exportMenuJson = array();
        foreach ($jsonMenuKeys as $value){
            $exportMenuJson[$value] = true;
        }
        $exportMenuJson = $this->setMenuOnOff($exportMenuJson);
        $backendBaseBundlePath = $this->getContainer()->get('kernel')->getBundle('BackendBaseBundle')->getPath();
        file_put_contents("{$backendBaseBundlePath}/Resources/vue/components/config/env/menuBundle.json", json_encode($exportMenuJson));
        $io->text('Dump menu JSON file');
    }

    protected function dumpAllBundleJson(SymfonyStyle $io)
    {
        $backendBaseBundlePath = $this->getContainer()->get('kernel')->getBundle('BackendBaseBundle')->getPath();
        $bundles = $this->getContainer()->getParameter('kernel.bundles');
        file_put_contents("{$backendBaseBundlePath}/Resources/vue/components/config/env/allBundle.json", json_encode($bundles));
        $io->text('Dump all bundle JSON file');
    }

    protected function setMenuOnOff($menuJson)
    {
        $bundles = $this->getContainer()->getParameter('kernel.bundles');
        if( in_array("Widget\\ProductStyleBundle\\WidgetProductStyleBundle", $bundles)){
            $menuJson['menu.stock'] = false;
            $menuJson['menu.stock_style'] = true;
            $menuJson['menu.styletemplate'] = true;
        }
        else {
            $menuJson['menu.stock'] = true;
            $menuJson['menu.stock_style'] = false;
            $menuJson['menu.styletemplate'] = false;
        }
        return $menuJson;
    }

    protected function notifyDynamicCustomConfig(BundleInterface $bundle, $targetDir)
    {
        $event = new DynamicCustomConfigEvent($bundle->getName(), $targetDir);
        $this->eventDispatcher->dispatch(DynamicCustomConfigEvent::EVENT_NAME, $event);
    }

}
