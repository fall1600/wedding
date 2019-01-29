<?php
namespace Backend\BaseBundle\Command;

use Backend\BaseBundle\Event\TestInitEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestInitCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('dgfactor:test:init')
            ->setDescription('初始化測試用的資料.')
            ->addOption('force', null, InputOption::VALUE_NONE, '請加入這個參數，確認執行')
            ->setHelp(<<<EOT
<info>dgfactor:test:init</info> 初始化測試用的資料.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        if($container->get('kernel')->getEnvironment() != 'test'){
            $output->writeln("<error>必須在測試環境底下執行</error>");
            return;
        }

        if(!$input->getOption('force')){
            $output->writeln("<error>請加上 --force 確認執行</error>");
            return;
        }

        $this->doPropelBuild($input, $output);
        $this->doPropelSqlInsert($input, $output);
        $this->doFixtureLoad($input, $output);
        $this->doDispatchEvent($input, $output);
    }

    protected function doPropelBuild(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('propel:build');
        $command->run(new ArrayInput(array(
            'command' => 'propel:build',
            '-e' => 'test'
        )), $output);
    }

    protected function doPropelSqlInsert(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('propel:sql:insert');
        $command->run(new ArrayInput(array(
            'command' => 'propel:sql:insert',
            '--force' => '--force',
            '-e' => 'test',
        )), $output);
    }

    protected function doFixtureLoad(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('dgfactor:fixtures:load');
        $command->run(new ArrayInput(array(
            'command' => 'dgfactor:fixtures:load',
            '-e' => 'test',
            '--force' => '--force',
        )), $output);
    }

    protected function doDispatchEvent(InputInterface $input, OutputInterface $output)
    {
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');
        $eventDispatcher->dispatch(TestInitEvent::EVENT_TEST_INIT, new TestInitEvent($input, $output));
    }

}
