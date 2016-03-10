<?php

/**
 * Description of RecNoDebo
 *
 * @author gabriel
 */

namespace BDS\DoctrineBundle\Eventos;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Hallazgos {
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        
    }     
    public function postPersist(LifecycleEventArgs $args)
    {
        $hallazgo = $args->getEntity();
        
        if($hallazgo instanceof \Ministerio\SigesBundle\Entity\Hallazgos ){
            try {
                $parametros = array();
                $parametros['actual']= $hallazgo;
                $this->container->get('auditor')->grabarPistaAuditoriaAlta($parametros );
                
            } catch (\Doctrine\DBAL\DBALException $exc) {
                $mensaje = $exc->getMessage();
                die($mensaje);
            }
        }
    }
    public function postUpdate(LifecycleEventArgs $args)
    {
        
        //die("Actualizando");
        $hallazgo = $args->getEntity();
        
        if($hallazgo instanceof \Ministerio\SigesBundle\Entity\Hallazgos ){
            try {
                $parametros = array();
                $parametros['actual']= $hallazgo;
                //$this->container->get('auditor')->grabarPistaAuditoriaAlta($parametros );
                
            } catch (\Doctrine\DBAL\DBALException $exc) {
                $mensaje = $exc->getMessage();
                die($mensaje);
            }
        }
    }

    
}
