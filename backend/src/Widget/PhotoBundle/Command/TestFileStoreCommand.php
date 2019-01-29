<?php

namespace Widget\PhotoBundle\Command;

use Backend\BaseBundle\FileStore\FileStoreInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestFileStoreCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('dgfactor:test:filestore')
            ->setDescription('測試filestore 是否可以寫入')
            ->addOption('force', null, InputOption::VALUE_NONE, '請加入這個參數，確認執行')
            ->setHelp(<<<EOT
<info>dgfactor:test:filestore</info> 測試filestore 是否可以寫入.
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

        $this->main($output);
    }

    protected function main(OutputInterface $output)
    {
        $ext = 'jpg';
        $suffix = 'origin';
        $prefix = 'test_s3';
        $filename = tempnam(sys_get_temp_dir(), $prefix);
        $uid = md5($filename);
        file_put_contents($filename, "123");

        /** @var FileStoreInterface $service */
        $service = $this->getContainer()->get("widget_photo.file_store");
        try {
            $service->write($uid, $suffix, $ext, $filename);
            $service->delete($uid, $suffix, $ext);
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
        } finally {
            unlink($filename);
            $output->writeln("<info>test done</info>");
        }
    }
}
