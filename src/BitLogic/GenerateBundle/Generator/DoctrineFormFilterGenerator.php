<?php

namespace BitLogic\GenerateBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Sensio\Bundle\GeneratorBundle\Generator\Generator as Generator;

/**
 * Class DoctrineFormGenerator
 *
 * @author gabriel
 */
class DoctrineFormFilterGenerator extends Generator
{
    /**
     * @var string
     */
    private $skeletonDir;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $classPath;

    /**
     * @param string $skeletonDir
     */
    public function __construct($skeletonDir)
    {
        $this->setSkeletonDirs($skeletonDir);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getClassPath()
    {
        return $this->classPath;
    }

    /**
     * Generates the document form class if it does not exist.
     *
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param string                                               $document
     * @param \Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo      $metadata
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'FilterType';
        $dirPath         = $bundle->getPath().'/Form/Filter';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'FilterType.php';

        if (file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);
        

        $this->renderFile('form/FormFilterType.php.twig', $this->classPath, array(
            //'fields'           => $this->getFieldsFromMetadata($metadata),
            'fields'           => $metadata->fieldMappings,
            'namespace'        => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class'     => $entityClass,
            'bundle'           => $bundle->getName(),
            'form_class'       => $this->className,
            'form_type_name'   => strtolower(str_replace('\\', '_', $bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.substr($this->className, 0, -4)),
        ));
    }

    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param \Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo $metadata
     *
     * @return array
     */
    private function getFieldsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fields = (array) $metadata->getFieldNames();

        // Remove the primary key field if it's not managed manually
        if ($metadata->isIdGeneratorAuto()) {
            $fields = array_diff($fields, array($metadata->identifier));
        }

        return $fields;
    }
}
