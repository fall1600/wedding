<?php
namespace Backend\BaseBundle\Command;

use Backend\BaseBundle\Model\Site;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Backend\BaseBundle\Model;

/**
 * @author Lenar Lõhmus <lenar@city.ee>
 */
abstract class RoleCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, '帳號'),
                new InputArgument('role', InputArgument::OPTIONAL, '權限'),
                new InputOption('super', null, InputOption::VALUE_NONE, '快速賦予管理源權限'),
            ));
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');
        $super = (true === $input->getOption('super'));
        $siteUserProvider = $this->getContainer()->get('site_user_provider');
        $siteUserManager = $this->getContainer()->get('site_user_manager');

        $siteUser = $siteUserProvider->loadUserByUsernameOrEmail($username);

        if(!$siteUser){
            $output->writeln(sprintf('找不到帳號 <error>%s</error>', $username));
            return;
        }

        if (null !== $role && $super) {
            throw new \InvalidArgumentException('You can pass either the role or the --super option (but not both simultaneously).');
        }

        if (null === $role && !$super) {
            throw new \RuntimeException('Not enough arguments.');
        }
        $this->executeRoleCommand($output, $siteUser, $super, $role);
    }

    abstract protected function executeRoleCommand(OutputInterface $output, Model\SiteUser $siteuser, $super, $role);

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username:',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }
        if ((true !== $input->getOption('super')) && !$input->getArgument('role')) {
            $role = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a role:',
                function($role) {
                    if (empty($role)) {
                        throw new \Exception('Role can not be empty');
                    }

                    return $role;
                }
            );
            $input->setArgument('role', $role);
        }
    }
}
