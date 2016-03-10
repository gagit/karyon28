<?php


/**
 *
 * @author Gabriel Toledo
 */

namespace Gen\DocBundle\Modelo;

interface EtiquetaInterface {
    
    public function getId();
    public function setId($id);
    public function getNombreEtiqueta();
    public function setNombreEtiqueta($nombre_etiqueta);
    
}

?>
