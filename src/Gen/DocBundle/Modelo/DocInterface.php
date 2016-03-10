<?php
/**
 *
 * @author Gabriel Toledo
 */
namespace Gen\DocBundle\Modelo;


interface DocInterface {
    
    public function getId();
    public function setId($id);
    public function getNombreDocumento();
    public function setNombreDocumento($nombre_documento);
    public function getExtension();
    public function setExtension($extension);
    public function getFechaCreacion();
    public function setFechaCreacion($fecha_creacion);
    public function getFechaUltimaModificacion();
    public function setFechaUltimaModificacion($fecha_ultima_modificacion);
    public function getFechaExpiracion();
    public function setFechaExpiracion($fecha_expiracion);
    public function getTipoMime();
    public function setTipoMime($tipo_mime);
    public function getTamanio();
    public function setTamanio($tamanio);
    /* Dejo la implementacion para despues ...   
    public function getEtiquetas();
    public function setEtiquetas($etiqueta);
     */
    
}

?>
