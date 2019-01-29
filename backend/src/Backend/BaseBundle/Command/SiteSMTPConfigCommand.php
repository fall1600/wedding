<?php
namespace Backend\BaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Backend\BaseBundle\Model;

class SiteSMTPConfigCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('dgfactor:siteconfig:SMTP')
            ->setDescription('建立寄信主機設定.')
            ->setDefinition(array(
                new InputArgument('host', InputArgument::REQUIRED, 'SMTP主機'),
                new InputArgument('post', InputArgument::REQUIRED, 'SMTP通訊埠'),
                new InputArgument('sender_name', InputArgument::REQUIRED, '寄件人名稱'),
                new InputArgument('sender_mail', InputArgument::REQUIRED, '寄件人Mail')
            ))
            ->setHelp(<<<EOT
<info>dgfactor:siteconfig:SMTP</info> 建立寄信主機設定.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configData = array(
            'host'        => $input->getArgument('host'),
            'post'        => $input->getArgument('post'),
            'sender_name' => $input->getArgument('sender_name'),
            'sender_mail' => $input->getArgument('sender_mail')
        );

        if ($siteConfig = Model\SiteConfigQuery::create()->findOneByName('mail')){
            $siteConfig->setConfig($configData)->save();
            return;
        }
        $siteSMTPConfig = new Model\SiteConfig();
        $siteSMTPConfig->setName('mail')
            ->setConfig($configData);
        $output->writeln(print_r('Created SMTP config'));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('host')) {
            $host = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a host:',
                function($host) {
                    if (empty($host)) {
                        throw new \Exception('Host can not be empty');
                    }

                    return $host;
                }
            );
            $input->setArgument('host', $host);
        }

        if (!$input->getArgument('post')) {
            $post = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an post:',
                function($post) {
                    if (empty($post)) {
                        throw new \Exception('Post can not be empty');
                    }

                    return $post;
                }
            );
            $input->setArgument('post', $post);
        }

        if (!$input->getArgument('sender_name')) {
            $senderName = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a SenderName:',
                function($senderName) {
                    if (empty($senderName)) {
                        throw new \Exception('SenderName can not be empty');
                    }

                    return $senderName;
                }
            );
            $input->setArgument('sender_name', $senderName);
        }

        if (!$input->getArgument('sender_mail')) {
            $senderMail = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a SenderMail:',
                function($senderMail) {
                    if (empty($senderMail)) {
                        throw new \Exception('SenderMail can not be empty');
                    }

                    return $senderMail;
                }
            );
            $input->setArgument('sender_mail', $senderMail);
        }
    }
}