<?php

/*
 * This file is part of the Doctrine MongoDBBundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mparaiso\Doctrine\ODM\MongoDB\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Bundle\MongoDBBundle\Command\InfoDoctrineODMCommand as BaseInfoDoctrineODMCommand;
/**
 * Show information about mapped documents
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class InfoDoctrineODMCommand extends BaseInfoDoctrineODMCommand
{
    

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getHelper("app")->getApplication();
        $managerRegistry = $app['odm.manager_registry'];
        $documentManagerName = $input->getOption('dm') ?
            $input->getOption('dm') :
            $managerRegistry->getDefaultManagerName();

        /* @var $documentManager Doctrine\ODM\MongoDB\DocumentManager */
        $documentManager = $managerRegistry->getManager($documentManagerName);

        $documentClassNames = $documentManager->getConfiguration()
                                          ->getMetadataDriverImpl()
                                          ->getAllClassNames();

        if (!$documentClassNames) {
            throw new \Exception(
                'You do not have any mapped Doctrine MongoDB ODM documents for any of your bundles. '.
                'Create a class inside the Document namespace of any of your bundles and provide '.
                'mapping information for it with Annotations directly in the classes doc blocks '.
                'or with XML/YAML in your bundles Resources/config/doctrine/metadata/mongodb directory.'
            );
        }

        $output->write(sprintf("Found <info>%d</info> documents mapped in document manager <info>%s</info>:\n",
            count($documentClassNames), $documentManagerName), true);

        foreach ($documentClassNames AS $documentClassName) {
            try {
                $cm = $documentManager->getClassMetadata($documentClassName);
                $output->write("<info>[OK]</info>   " . $documentClassName, true);
            } catch(\Exception $e) {
                $output->write("<error>[FAIL]</error> " . $documentClassName, true);
                $output->write("<comment>" . $e->getMessage()."</comment>", true);
                $output->write("", true);
            }
        }
    }
}
