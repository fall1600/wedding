<?php
namespace Backend\BaseBundle\Command;

use Backend\BaseBundle\Event\TestInitEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SiteInitCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('dgfactor:site:init')
            ->setDescription('建立可以登入後台的環境.')
            ->addOption('force', null, InputOption::VALUE_NONE, '請加入這個參數，確認執行')
            ->setHelp(<<<EOT
<info>dgfactor:site:init</info> 初始化測試用的資料.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        if(!$input->getOption('force')){
            $output->writeln("<error>請加上 --force 確認執行</error>");
            return;
        }

        $this->doPropelBuild($input, $output);
        $this->doPropelSqlInsert($input, $output);
        $this->doCreateUser($input, $output);
        $this->doPromoteUser($input, $output);
    }

    protected function doPropelBuild(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('propel:build');
        $command->run(new ArrayInput(array(
            'command' => 'propel:build'
        )), $output);
    }

    protected function doPropelSqlInsert(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('propel:sql:insert');
        $command->run(new ArrayInput(array(
            'command' => 'propel:sql:insert',
            '--force' => '--force'
        )), $output);
    }

    protected function doCreateUser(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('dgfactor:siteuser:create');
        $command->run(new ArrayInput(array(
            'command' => 'dgfactor:siteuser:create',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => '12853714',
        )), $output);
    }

    protected function doPromoteUser(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('dgfactor:siteuser:promote');
        $command->run(new ArrayInput(array(
            'command' => 'dgfactor:siteuser:promote',
            'username' => 'admin',
            'role' => 'ROLE_SUPERADMIN',
        )), $output);
    }
}
