<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            //----------
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new BDS\DoctrineBundle\BDSDoctrineBundle(),
            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Gen\DocBundle\DocBundle(),
            new Gen\uiBundle\GenuiBundle(),
            new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
            new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            //--------------
            new BitLogic\BaseBundle\BaseBundle(),
            new BitLogic\GenerateBundle\GenerateBundle(),

        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
