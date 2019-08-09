<?php

namespace BitLogic\GenerateBundle\Command;

use BitLogic\GenerateBundle\Generator\DoctrineCrudGenerator;
use BitLogic\GenerateBundle\Generator\DoctrineFormGenerator;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class GenerateDoctrineCrudCommand extends GenerateDoctrineCommand
{
    /**
     * @var DoctrineCrudGenerator
     */
    private $generator;

    /**
     * @var DoctrineFormGenerator
     */
    private $formGenerator;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('document', '', InputOption::VALUE_REQUIRED, 'El nombre de la clase Documento para incializar (notación corta)'),
                new InputOption('route-prefix', '', InputOption::VALUE_REQUIRED, 'El prefijo de la ruta - The route prefix'),
                new InputOption('controller-name', '', InputOption::VALUE_REQUIRED, 'El nombre del controlador - The controller name'),
                new InputOption('with-write', '', InputOption::VALUE_NONE, 'Opción para generar las acciones create, new y delete'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Usar para indicar el formato de los archivos de configuración(php, xml, yml, or annotation)', 'annotation'),
            ))
            ->setDescription('Genera un CRUD basado en un Documento Doctrine')
            ->setHelp(<<<EOT
The <info>doctrine:mongodb:generate:crud</info> command generates a CRUD based on a Doctrine document.

The default command only generates the list and show actions.

<info>php app/console doctrine:mongodb:generate:crud --document=AcmeBlogBundle:Post --route-prefix=post_admin</info>

Using the --with-write option allows to generate the new, edit and delete actions.

<info>php app/console doctrine:mongodb:generate:crud --document=AcmeBlogBundle:Post --route-prefix=post_admin --with-write</info>
EOT
            )
            ->setName('doctrine:mongodb:generate:crud')
            ->setAliases(array('generate:doctrine:mongodb:crud'));
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            if (!$dialog->ask($input, $output, new ConfirmationQuestion($dialog->getQuestion('Confirma la generación del código', 'yes', '?')), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $document = Validators::validateDocumentName($input->getOption('document'));
        list($bundle, $document) = $this->parseShortcutNotation($document);

        $format    = Validators::validateFormat($input->getOption('format'));
        $prefix    = $this->getRoutePrefix($input, $document);
        $withWrite = $input->getOption('with-write');

        $dialog->writeSection($output, 'Generación del CRUD');

        $documentClass = $this->getDocumentNamespace($bundle).'\\'.$document;
        $metadata      = $this->getDocumentMetadata($documentClass);
        $bundle        = $this->getBundle($bundle);

        $generator = $this->getGenerator();
        $generator->generate($bundle, $document, $metadata, $format, $prefix, $withWrite);

        $output->writeln('Generando el código del CRUD: <info>OK</info>');

        $errors = array();
        $runner = $dialog->getRunner($output, $errors);

        // form
        if ($withWrite) {
            $this->generateForm($bundle, $document, $metadata);
            $output->writeln('Generando el código del Form : <info>OK</info>');
        }

        // routing
        if ('annotation' != $format) {
            call_user_func($runner, $this->updateRouting($dialog, $input, $output, $bundle, $format, $document, $prefix));
        }

        $dialog->writeGeneratorSummary($output, $errors);

        return 0;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getQuestionHelper();
        $dialog->writeSection($output, 'Bienvenidos al Generador de CRUDs para Documentos Doctrine2-MognoDB');

        // namespace
        $output->writeln(array(
            '',
            'Este comando le ayuda a generar controladores y templates de las operaciones CRUD.',
            '',
            'Primero, necesita indicar el documento para el que quiere generar el CRUD.',
            'Usted puede dar un documento que aún no existe y el asistente le ayudará a definirlo.',
            'Debe usar la notación corta como esta <comment>AcmeBlogBundle:Post</comment>.',
            '',
        ));

        list($document, $bundle) = $this->askForDocument($input, $output, $dialog);

        $this->askForWriteOption($input, $output, $dialog);

        $format = $this->askForMappingFormat($input, $output, $dialog);

        $this->askForRoutePrefix($input, $output, $dialog, $document);

        // summary
        $output->writeln(array(
            '',
            $this->getFormatter()->formatBlock('Resumén de la generación', 'bg=blue;fg=white', true),
            '',
            sprintf("Usted va a generar un controlador CRUD para \"<info>%s:%s</info>\"", $bundle, $document),
            sprintf("usando el formato de configuración \"<info>%s</info>\".", $format),
            '',
        ));
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param string                                          $document
     *
     * @return string
     */
    protected function getRoutePrefix(InputInterface $input, $document)
    {
        $prefix = $input->getOption('route-prefix') ?: strtolower(str_replace(array('\\', '/'), '_', $document));

        if ($prefix && '/' === $prefix[0]) {
            $prefix = substr($prefix, 1);
        }

        return $prefix;
    }

    /**
     * @return DoctrineCrudGenerator
     */
    protected function getGenerator()
    {
        if (null === $this->generator) {
            $this->generator = new DoctrineCrudGenerator($this->getFilesystem(), $this->getSkeletonPath().'/crud');
        }

        return $this->generator;
    }

    /**
     * @param DoctrineCrudGenerator $generator
     */
    public function setGenerator(DoctrineCrudGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return DoctrineFormGenerator
     */
    protected function getFormGenerator()
    {
        if (null === $this->formGenerator) {
            $this->formGenerator = new DoctrineFormGenerator($this->getSkeletonPath().'/form');
        }

        return $this->formGenerator;
    }

    /**
     * @param DoctrineFormGenerator $formGenerator
     */
    public function setFormGenerator(DoctrineFormGenerator $formGenerator)
    {
        $this->formGenerator = $formGenerator;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param QuestionHelper  $dialog
     *
     * @return array
     */
    private function askForDocument(InputInterface $input, OutputInterface $output, QuestionHelper $dialog)
    {
        $question = new Question($dialog->getQuestion('Nombre corto del Documento', $input->getOption('document')), $input->getOption('document'));
        $question->setValidator(array(
            'BitLogic\GenerateBundle\Command\Validators',
            'validateDocumentName',
        ));

        $document = $dialog->ask($input, $output, $question, false, $input->getOption('document'));
        $input->setOption('document', $document);
        list($bundle, $document) = $this->parseShortcutNotation($document);

        return array($document, $bundle);
    }

    /**
     * Tries to generate forms if they don't exist yet and if we need write operations on documents.
     */
    private function generateForm(BundleInterface $bundle, $document, $metadata)
    {
        try {
            $this->getFormGenerator()->generate($bundle, $document, $metadata);
        } catch (\RuntimeException $e) {
            // form already exists
        }
    }

    /**
     * @param QuestionHelper  $dialog
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param BundleInterface $bundle
     * @param string          $format
     * @param string          $document
     * @param string          $prefix
     *
     * @return array|null
     */
    private function updateRouting(QuestionHelper $dialog, InputInterface $input, OutputInterface $output, BundleInterface $bundle, $format, $document, $prefix)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $auto = $dialog->ask($input, $output, new ConfirmationQuestion($dialog->getQuestion('Confirm automatic update of the Routing', 'yes', '?')), true);
        }

        $configPath  = $bundle->getPath().'/Resources/config';
        $routingFile = $configPath.'/routing.yml';

        $output->write('Importando las rutas de las CRUD: ');
        $this->getFilesystem()->mkdir($configPath);
        $routing = new RoutingManipulator($routingFile);

        try {
            $ret = $auto ? $routing->addResource($bundle->getName(), $format, '/'.$prefix, 'routing/'.strtolower(str_replace('\\', '_', $document))) : false;
        } catch (\RuntimeException $exc) {
            $ret = false;
        }

        if ($ret) {
            return;
        }

        $help = sprintf("        <comment>resource: \"@%s/Resources/config/routing/%s.%s\"</comment>\n", $bundle->getName(), strtolower(str_replace('\\', '_', $document)), $format);
        $help .= sprintf("        <comment>prefix:   /%s</comment>\n", $prefix);

        return array(
            '- Import the bundle\'s routing resource in the bundle routing file',
            sprintf('  (%s).', $routingFile),
            '',
            sprintf('    <comment>%s:</comment>', $bundle->getName().('' !== $prefix ? '_'.str_replace('/', '_', $prefix) : '')),
            $help,
            '',
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param QuestionHelper  $dialog
     */
    private function askForWriteOption(InputInterface $input, OutputInterface $output, QuestionHelper $dialog)
    {
        $withWrite = $input->getOption('with-write') ?: false;
        $output->writeln(array(
            '',
            'Por defecto, el generador crea 2 acciones: list and show.',
            'También puede seleccionar las acciones del escritura: new, update, and delete.',
            '',
        ));
        $question  = new ConfirmationQuestion($dialog->getQuestion('Quiere seleccionar las acciones del escritura', $withWrite ? 'yes' : 'no', '?'), $withWrite);
        $withWrite = $dialog->ask($input, $output, $question);
        $input->setOption('with-write', $withWrite);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param QuestionHelper  $dialog
     *
     * @return mixed
     */
    private function askForMappingFormat(InputInterface $input, OutputInterface $output, QuestionHelper $dialog)
    {
        $format = $input->getOption('format');
        $output->writeln(array(
            '',
            'Indique el formato para generar CRUD.',
            '',
        ));
        $question = new Question($dialog->getQuestion('Formatos de configuración (yml, xml, php, or annotation)', $format), $format);
        $question->setValidator(array(
            'Sensio\Bundle\GeneratorBundle\Command\Validators',
            'validateFormat',
        ));

        $format = $dialog->ask($input, $output, $question);
        $input->setOption('format', $format);

        return $format;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param QuestionHelper  $dialog
     * @param                 $document
     */
    private function askForRoutePrefix(InputInterface $input, OutputInterface $output, QuestionHelper $dialog, $document)
    {
        $prefix = $this->getRoutePrefix($input, $document);
        $output->writeln(array(
            '',
            'Indique el prefijo de las rutas,(todas las rutas se crearan con este prefijo',
            'prefix: /prefix/, /prefix/new, ...).',
            '',
        ));
        $question = new Question($dialog->getQuestion('Prefijo de las rutas', '/'.$prefix), '/'.$prefix);
        $prefix   = $dialog->ask($input, $output, $question, '/'.$prefix);
        $input->setOption('route-prefix', $prefix);
    }
}
