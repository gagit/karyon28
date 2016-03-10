<?php
/**
 *
 * @author Gabriel Toledo
 */

namespace Gen\DocBundle\Modelo;

interface MarcadorInterface {
    
    public function getId();
    public function setId($id);
    public function getNombreMarcador();
    public function setNombreMarcador($nombre_marcador);
    public function getDescripcionMarcador();
    public function setDescripcionMarcador($descripcion_marcador);
    public function getClaseMarcador();
    public function setClaseMarcador($clase_marcador);
    
}

?>
