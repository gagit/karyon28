<?php

/**
 * Description of RecNoDebo
 *
 * @author gabriel
 */

namespace BDS\DoctrineBundle\Eventos;

use Doctrine\ORM\Event\LifecycleEventArgs;


class NroInforme {
    
    public function prePersist(LifecycleEventArgs $args)
    {
        $solicitudInforme = $args->getEntity();
        if($solicitudInforme instanceof \Ministerio\InformesBundle\Entity\SolicitudInforme ){
            $entityManager = $args->getEntityManager();
            $nombreSecuencia = 'SOLICITUDINFORME_NRO_SEQ';
            $conn = $entityManager->getConnection();

            try {
                $sql = $conn->getDatabasePlatform()->getSequenceNextValSQL($nombreSecuencia);
                $valor = $conn->fetchColumn($sql);
                $solicitudInforme->setNroSolicitud($valor);
            } catch (\Doctrine\DBAL\DBALException $exc) {
                $mensaje = $exc->getMessage();
                die($mensaje);
            }
        
        }
    }

    
}
