<?php
namespace Backend\BaseBundle\Command;

use Propel\Bundle\PropelBundle\Command\DatabaseCreateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDatabaseCommand extends DatabaseCreateCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDescription('建立資料庫')
            ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'Set this parameter to define a connection to use')
            ->setName('dgfactor:database:create');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($name, $config) = $this->getConnection($input, $output);
        $dbName = $this->parseDbName($config['connection']['dsn']);

        if (null === $dbName) {
            return $output->writeln('<error>No database name found.</error>');
        } else {
            $query  = 'CREATE DATABASE '. $dbName .' CHARACTER SET utf8 COLLATE utf8_unicode_ci;';
        }

        try {
            \Propel::setConfiguration($this->getTemporaryConfiguration($name, $config));
            $connection = \Propel::getConnection($name);

            $statement = $connection->prepare($query);
            $statement->execute();

            $output->writeln(sprintf('<info>Database <comment>%s</comment> has been created.</info>', $dbName));
        } catch (\Exception $e) {
            $this->writeSection($output, array(
                '[Propel] Exception caught',
                '',
                $e->getMessage()
            ), 'fg=white;bg=red');
        }
    }
}