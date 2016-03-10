<?php
/**
 *
 * @author Gabriel Toledo
 */
namespace Gen\DocBundle\Modelo;


interface ContainerDocInterface {
    
    public function getTipoContenedor();
    public function setTipoContenedor($tipo_contenedor);
    public function getURL();
    public function setURL($url);
    public function getProtocolo();
    public function setProtocolo($protocolo);
    
}

?>
