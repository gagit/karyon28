<?php

/**
 * Description of RecNoDebo
 *
 * @author gabriel
 */

namespace BDS\DoctrineBundle\Eventos;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;


class Allanamientos {
    
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        
    } 
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $allanamiento = $args->getEntity();
        if($allanamiento instanceof \Ministerio\SigesBundle\Entity\Allanamiento ){
            try {
                $parametros = array();
                $parametros['actual']= $allanamiento;
                $this->container->get('auditor')->grabarPistaAuditoriaAlta($parametros );
            } catch (\Doctrine\DBAL\DBALException $exc) {
                $mensaje = $exc->getMessage();
                die($mensaje);
            }
        }
    }

    
}
