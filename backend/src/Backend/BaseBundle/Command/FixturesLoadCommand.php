<?php
namespace Backend\BaseBundle\Command;

use Backend\BaseBundle\Event\TestInitEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

class FixturesLoadCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('dgfactor:fixtures:load')
            ->setDescription('讀取 Fixtures data.')
            ->addOption('force', null, InputOption::VALUE_NONE, '請加入這個參數，確認執行')
            ->setHelp(<<<EOT
<info>dgfactor:fixtures:load</info> 初始化測試用的資料.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(!$input->getOption('force')){
            $output->writeln("<error>請加上 --force 確認執行</error>");
            return;
        }

        $tmpFixtureFolder = $this->createTempFixtureFolder();
        $this->copyFixtures($tmpFixtureFolder, $input, $output);
        $this->doPropelFixtureLoad($input, $output, $tmpFixtureFolder);
        $this->cleanFolder($tmpFixtureFolder);
    }

    protected function copyFixtures($tmpFixtureFolder, InputInterface $input, OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        foreach ($this->findFixtures($input, $output) as $file) {
            $fileSystem->copy($file->getPathname(), $tmpFixtureFolder."/{$file->getFilename()}");
        }
    }

    protected function cleanFolder($folder)
    {
        $finder = new Finder();
        $fileSystem = new Filesystem();
        $fileSystem->remove($finder->in($folder)->files());
        $fileSystem->remove($folder);
    }

    protected function createTempFixtureFolder()
    {
        $fileSystem = new Filesystem();
        $container = $this->getContainer();
        $kernel = $container->get('kernel');
        $tmpFolder = $kernel->getCacheDir()."/fixture-".md5(microtime().rand());
        $fileSystem->mkdir($tmpFolder);
        return $tmpFolder;
    }

    /**
     * @return File[]
     */
    protected function findFixtures(InputInterface $input, OutputInterface $output)
    {
        $fixtureSearchPath = array();
        $container = $this->getContainer();
        $kernel = $container->get('kernel');

        $baseFixturePath = "{$kernel->getRootDir()}/propel/fixtures";
        if(is_dir($baseFixturePath)){
            $output->writeln("<info>search $baseFixturePath</info>");
            $fixtureSearchPath[] = $baseFixturePath;
        }

        foreach($kernel->getBundles() as $bundle){
            $path = "{$bundle->getPath()}/Resources/fixtures";
            if(is_dir($path)){
                $output->writeln("<info>search {$bundle->getName()}</info>");
                $fixtureSearchPath[] = $path;
            }
        }

        $finder = new Finder();
        foreach($finder->in($fixtureSearchPath)->files()->name('*.yml') as $file){
            yield $file;
        }
    }

    protected function doPropelFixtureLoad(InputInterface $input, OutputInterface $output, $tmpFixtureFolder)
    {
        $kernel = $this->getContainer()->get('kernel');
        $env = $kernel->getEnvironment();
        $dir = substr($tmpFixtureFolder, strlen(realpath($this->getApplication()->getKernel()->getRootDir() . '/../')) + 1);
        $command = $this->getApplication()->find('propel:fixtures:load');
        $command->run(new ArrayInput(array(
            'command' => 'propel:fixtures:load',
            '-e' => $env,
            '--dir' => $dir,
        )), $output);
    }

}
