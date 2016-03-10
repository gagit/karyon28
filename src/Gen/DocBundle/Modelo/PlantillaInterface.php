<?php

/**
 *
 * @author Gabriel Toledo
 */

namespace Gen\DocBundle\Modelo;

interface PlantillaInterface {
    
    public function getId();
    public function setId($id);
    public function getNombrePlantilla();
    public function setNombrePlantilla($nombre_plantilla);
    public function getNombreCortoPlantilla();
    public function setNombreCortoPlantilla($nombre_corto_plantilla);
    public function getDescripcionPlantilla();
    public function setDescripcionPlantilla($desc_plantilla);
    public function getPlantilla();
    public function setPlantilla($plantilla);

}

?>
