<?php
namespace Widget\PhotoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Widget\PhotoBundle\Model\PhotoConfig;
use Widget\PhotoBundle\Model\PhotoConfigQuery;

/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2016/12/9
 * Time: 上午 11:26
 */
class PhotoDefaultConfigCommand extends ContainerAwareCommand
{
    /** 寫排程器 */
    protected function configure()
    {
        $this
            ->setName('dgfactor:photo:defaultConfig')
            ->setDescription('建立預設照片設定')
            ->setHelp(<<<EOT
The <info>dgfactor:photo:defaultConfig</info> 建立 預設 照片設定:

  <info>php app/console dgfactor:photo:defaultConfig dgfactor</info>
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configData = array(
            array(
                "type" => "inbox",
                "suffix" => "large",
                "width" => 1600,
                "height" => 1200
            ),
            array(
                "type" => "inbox",
                "suffix" => "middle",
                "width" => 800,
                "height" => 600
            ),
            array(
                "type" => "inbox",
                "suffix" => "small",
                "width" => 400,
                "height" => 300
            ),
            array(
                "type" => "inbox",
                "suffix" => "tiny",
                "width" => 120,
                "height" => 90
            ));
        /** 設定照片default設定檔 */
        $defaultPhotoConfig = PhotoConfigQuery::create();
        if ($defaultPhotoConfig->findOneByName('default')){
            throw new \Exception("default config is exist!");
        }
        $photoDefaultConfig = new PhotoConfig();
        $photoDefaultConfig
            ->setBrief('default')
            ->setConfig($configData)
            ->setName('default')
            ->save()
        ;

    }
}