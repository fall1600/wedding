<?php
namespace Backend\BaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Backend\BaseBundle\Model;

class CreateSiteUserCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('dgfactor:siteuser:create')
            ->setDescription('建立站台後台登入帳號.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, '帳號'),
                new InputArgument('email', InputArgument::REQUIRED, 'email'),
                new InputArgument('password', InputArgument::REQUIRED, '密碼'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ))
            ->setHelp(<<<EOT
The <info>dgfactor:siteuser:create</info> 建立站台後台登入帳號:

  <info>php app/console dgfactor:siteuser:create matthieu</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as the second and third arguments:

  <info>php app/console dgfactor:siteuser:create matthieu matthieu@example.com mypassword</info>

You can create a super admin via the super-admin flag:

  <info>php app/console dgfactor:siteuser:create admin --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>php app/console dgfactor:siteuser:create thibault --inactive</info>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        $siteUser = new Model\SiteUser();
        $siteUser->setLoginName($username);
        $siteUser->setPlainPassword($password);
        $siteUser->setEmail($email);
        $siteUser->setEnabled(!$inactive);
        if($superadmin){
            $siteUser->setRoles(array('ROLE_SUPER_ADMIN'));
        }
        $siteUserManager = $this->getContainer()->get('site_user_manager');
        $siteUserManager->updateUser($siteUser);
        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }

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

        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an email:',
                function($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askHiddenResponseAndValidate(
                $output,
                'Please choose a password:',
                function($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }
    }
}