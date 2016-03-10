<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ABMCommand
 *
 * @author Gabriel Toledo
 */

namespace BitLogic\GenerateBundle\Command;
 
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand as GeneradorBase;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use BitLogic\GenerateBundle\Generator\DoctrineAbmGenerator;
use BitLogic\GenerateBundle\Generator\DoctrineFormAbmGenerator;
use BitLogic\GenerateBundle\Generator\DoctrineFormFilterGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Sensio\Bundle\GeneratorBundle\Command\AutoComplete\EntitiesAutoCompleter;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineFormGenerator;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;

class ABMCommand extends GeneradorBase {
    
     /**
     * @var DoctrineCrudGenerator
     */
    protected $generator;
      /**
     * @var DoctrineFormGenerator
     */
    protected $formGenerator;
      /**
     * @var DoctrineFormFilterGenerator
     */
    protected $formFilterGenerator;
    
    protected function configure()
    {
        parent::configure();
 
        $this->setDefinition(array(
                new InputArgument('entity', InputArgument::OPTIONAL, 'El nombre de la Entity para hacer ABM(notación corta)'),
                new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'El nombre de la Entity para hacer ABM(notación corta)'),
                new InputOption('route-prefix', '', InputOption::VALUE_REQUIRED, 'Prefijo de la ruta'),
                new InputOption('with-write', '', InputOption::VALUE_NONE, 'Permite elegir si genera las acciones create, new y delete'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Formato de los archivos de configuración (php, xml, yml, or annotation)', 'annotation'),
                new InputOption('overwrite', '', InputOption::VALUE_NONE, 'Sobreescribe los controladores y formularios existentes, sobrescribe todos los archvios generados'),
            ));
        $this->setName('doctrine:generar:abm');
        $this->setDescription('Generador de ABM!');
        $this->setDescription('Genera un ABM (CRUD) basado en una entidad Doctrine');
        $this->setHelp(<<<EOT
El comando <info>doctrine:generar:abm</info> genera un ABM basado en una entity Doctrine. 
    Este sobreescribe <info>doctrine:generate:crud</info> que viene por defecto.
 

El comando por defecto genera solamente acciones de listar y mostrar (list y show).

<info>php app/console doctrine:generate:crud --entity=AcmeBlogBundle:Post --route-prefix=post_admin</info>

Usando la opcion --with-write se puede generar las acciones new, edit and delete.

<info>php app/console doctrine:generate:crud --entity=AcmeBlogBundle:Post --route-prefix=post_admin --with-write</info>


EOT
            );
    }
    
    
     /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Confirmás la generación del ABM', 'yes', '?'), true);
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $format = Validators::validateFormat($input->getOption('format'));
        $prefix = $this->getRoutePrefix($input, $entity);
        $withWrite = $input->getOption('with-write');
        $forceOverwrite = $input->getOption('overwrite');

        $questionHelper->writeSection($output, 'Generación del ABM');

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        $metadata    = $this->getEntityMetadata($entityClass);
        $bundle      = $this->getContainer()->get('kernel')->getBundle($bundle);
        ///--------------------
        
        $generator = $this->getGenerator($bundle);
        $generator->generate($bundle, $entity, $metadata[0], $format, $prefix, $withWrite, $forceOverwrite);

        $output->writeln('Generando el Código del ABM: <info>OK</info>');
        ///-----------------

        $errors = array();
        $runner = $questionHelper->getRunner($output, $errors);

        // form
        if ($withWrite) {
            $output->write('Generando el Código del Formulario: ');
            if ($this->generateForm($bundle, $entity, $metadata)) {
                $output->writeln('<info>Se creo el formulario!!!! :-) </info>');
            } else {
                $output->writeln('<comment>El formulario ya existe!, no se hizo nada</comment>');
            }
        }
        $output->writeln('<info>Generando Filtros</info>');
        if ($this->getFormFilterGenerator($bundle)->generate($bundle, $entity, $metadata[0])) {
                $output->writeln('<info>Se crearon los types de los filtros!!!! :-) </info>');
        } else {
            $output->writeln('<comment>El formulario ya existe!, no se hizo nada</comment>');
        }
        //$output->writeln('<info>Finaliz</info>');

        // routing
        if ('annotation' != $format) {
            $runner($this->updateRouting($questionHelper, $input, $output, $bundle, $format, $entity, $prefix));
        }

        $questionHelper->writeGeneratorSummary($output, $errors);
    }
    
    protected function getGenerator(BundleInterface $bundle = null)
    {
    
       if (null === $this->generator) { 
        $this->generator = new DoctrineAbmGenerator($this->getContainer()->get('filesystem'));
        $this->generator->setSkeletonDirs(__DIR__.'/../Resources/views/SensioGeneratorBundle/skeleton/');
       }
       return $this->generator;
       
    }
    
    protected function getFormGenerator($bundle = null)
    {
        if (null === $this->formGenerator) {}
            $this->formGenerator = new DoctrineFormAbmGenerator($this->getContainer()->get('filesystem'));
            $this->formGenerator->setSkeletonDirs(__DIR__.'/../Resources/views/SensioGeneratorBundle/skeleton/');
        
        return $this->formGenerator;
    }
    protected function getFormFilterGenerator($bundle = null)
    {
        if (null === $this->formFilterGenerator) {}
            $this->formFilterGenerator = new DoctrineFormFilterGenerator($this->getContainer()->get('filesystem'));
            $this->formFilterGenerator->setSkeletonDirs(__DIR__.'/../Resources/views/SensioGeneratorBundle/skeleton/');
        
        return $this->formFilterGenerator;
    }
    
  
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Bienvenidos al Generador de ABMs Doctrine2 CRUD ');

        // namespace
        $output->writeln(array(
            '',
            'Este comando le ayuda a generar las pantallas y los controladores del ABM.',
            '',
            '1ro, es necesario que indique la entity a la que quiere hacerle el ABM.',
            '',
            'Puede usar la notacion corta <comment>AcmeBlogBundle:Post</comment>.',
            '',
        ));

        if ($input->hasArgument('entity') && $input->getArgument('entity') != '') {
            $input->setOption('entity', $input->getArgument('entity'));
        }

        $question = new Question($questionHelper->getQuestion('Nombre corto de la Entity', $input->getOption('entity')), $input->getOption('entity'));
        $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'));

        $autocompleter = new EntitiesAutoCompleter($this->getContainer()->get('doctrine')->getManager());
        $autocompleteEntities = $autocompleter->getSuggestions();
        $question->setAutocompleterValues($autocompleteEntities);
        $entity = $questionHelper->ask($input, $output, $question);

        $input->setOption('entity', $entity);
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        // write?
        $withWrite = $input->getOption('with-write') ?: true;
        $output->writeln(array(
            '',
            'Por defecto, el generador crea 2 acciones: list y show.',
            'Si también quiere generar las acciones de escritura: new, update, y delete.',
            '',
        ));
        $question = new ConfirmationQuestion($questionHelper->getQuestion('Realmente quiere generar las acciones de escritura', $withWrite ? 'yes' : 'no', '?', $withWrite), $withWrite);

        $withWrite = $questionHelper->ask($input, $output, $question);
        $input->setOption('with-write', $withWrite);

        // format
        $format = $input->getOption('format');
        $output->writeln(array(
            '',
            'Determinar el formato de configuracion que va ha usar para la generación del ABM.',
            '',
        ));
        $question = new Question($questionHelper->getQuestion('Formato de configuración (yml, xml, php, or annotation)', $format), $format);
        $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateFormat'));
        $format = $questionHelper->ask($input, $output, $question);
        $input->setOption('format', $format);

        // route prefix
        $prefix = $this->getRoutePrefix($input, $entity);
        $output->writeln(array(
            '',
            'Determine el prefijo de la ruta (todas las rutas serán "acopladas" bajo este',
            'prefijo: /prefijo/, /prefijo/new, ...).',
            '',
        ));
        $prefix = $questionHelper->ask($input, $output, new Question($questionHelper->getQuestion('Routes prefix', '/'.$prefix), '/'.$prefix));
        $input->setOption('route-prefix', $prefix);

        // summary
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Resumén de la Generación', 'bg=blue;fg=white', true),
            '',
            sprintf("Se van a generar las acciones ABM para \"<info>%s:%s</info>\"", $bundle, $entity),
            sprintf("usando el formato de configuración \"<info>%s</info>\" .", $format),
            '',
        ));
    }

  
    
    
}
