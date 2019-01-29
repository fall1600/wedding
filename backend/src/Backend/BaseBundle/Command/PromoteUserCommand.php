<?php
namespace Backend\BaseBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Backend\BaseBundle\Model;

class PromoteUserCommand extends RoleCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('dgfactor:siteuser:promote')
            ->setDescription('賦予站台會員權限')
            ->setHelp(<<<EOT
The <info>fos:user:promote</info> command promotes a user by adding a role

  <info>php app/console fos:user:promote matthieu ROLE_CUSTOM</info>
  <info>php app/console fos:user:promote --super matthieu</info>
EOT
            );
    }

    protected function executeRoleCommand(OutputInterface $output, Model\SiteUser $siteuser, $super, $role)
    {
        if ($super) {
            $siteuser->setRoles(array('ROLE_SUPERADMIN'));
            $output->writeln(sprintf('User "%s" has been promoted as a super administrator.', $siteuser->getUsername()));
        } else {
            $roles = $siteuser->getRoles();

            if (!$siteuser->hasDefaultRole($role)) {
                $siteuser->addDefaultRole($role);
                $output->writeln(sprintf('Role "%s" has been added to user "%s".', $role, $siteuser->getUsername()));
            } else {
                $output->writeln(sprintf('User "%s" did already have "%s" role.', $siteuser->getUsername(), $role));
            }
        }

        $siteuser->save();
    }
}
