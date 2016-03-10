<?php
/**
 * Description of Documento
 *
 * @author Gabriel Toledo
 */

namespace Gen\DocBundle\Entity ;

use Doctrine\ORM\Mapping as ORM;
use Gen\DocBundle\Modelo\Documento as DocAbstracto;

class Documento extends DocAbstracto {
    
    /**
    * @ORM\Id
    * @ORM\Column(type="string", length=40)
    * @ORM\GeneratedValue(strategy="CUSTOM")
    * @ORM\CustomIdGenerator(class="BDS\DoctrineBundle\lib\Doctrine\ORM\Id\UuidGenerador")
    */ 
    protected $id;
    
    /**
    * @ORM\Column(type="string", length=100, nullable=false)
    */
    protected $nombre_documento;


    
    /**
    * @ORM\Column(type="string", length=10, nullable=true)
    */
    protected $extension;
    
    /**
    * @ORM\Column(type="datetime", nullable=false)
    */
    protected $fecha_creacion;
    
    /**
    * @ORM\Column(type="datetime", nullable=true)
    */
    protected $fecha_ultima_modificacion;
    
    /**
    * @ORM\Column(type="datetime", nullable=true)
    */
    protected $fecha_expiracion;
    
    /**
    * @ORM\Column(type="string", length=200, nullable=true)
    */
    protected $tipo_mime;
    
    /**
    * @ORM\Column(type="integer", nullable=true)
    */
    protected $tamanio;

    /**
    * @ORM\Column(type="string", length=20, nullable=true)
    */
    protected $tipo_contenedor;
    
    /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    protected $url;
    
    /**
    * @ORM\Column(type="string", length=20, nullable=true)
    */
    protected $protocolo;

//----------------------------    
    
    public function getId() {
        return $this->id;
    }

    public function getNombreDocumento() {
        return $this->nombre_documento;
        
    }
    

    public function getTamanio() {
        return $this->tamanio;
    }

    public function getTipoMime() {
        return $this->tipo_mime;        
    }
    
    public function getExtension() {
        return $this->extension;
    }

    public function getFechaCreacion() {
        return $this->fecha_creacion;
    }

    public function getFechaExpiracion() {
        return $this->fecha_expiracion;
    }

    public function getFechaUltimaModificacion() {
        return $this->fecha_ultima_modificacion;
    }


///----------------------------------    
    public function setId($id) {
        $this->id=$id;
    }

    public function setNombreDocumento($nombre_documento) {
        $this->nombre_documento=$nombre_documento;
    }
    

    public function setTamanio($tamanio) {
        $this->tamanio=$tamanio;
    }

    public function setTipoMime($tipo_mime) {
        $this->tipo_mime=$tipo_mime;
    }
    
    public function setExtension($extension) {
        $this->extension=$extension;
    }

    public function setFechaCreacion($fecha_creacion) {
        $this->fecha_creacion=$fecha_creacion;
    }

    public function setFechaExpiracion($fecha_expiracion) {
        $this->fecha_expiracion=$fecha_expiracion;
    }

    public function setFechaUltimaModificacion($fecha_ultima_modificacion) {
        $this->fecha_ultima_modificacion=$fecha_ultima_modificacion;
    }


//------------------------------   
    public function setTipoContenedor($tipo_contenedor) {
        $this->tipo_contenedor=$tipo_contenedor;
    }
    public function setURL($url) {
        $this->url=$url;
    }
    public function setProtocolo($protocolo) {
        $this->protocolo=$protocolo;
    }  
    
    public function getTipoContenedor() {
        return $this->tipo_contenedor;
    }
    
      
    public function getProtocolo() {
        return $this->protocolo;
    }
    
    public function getURL() {
        return $this->url;
    }

   


       //put your code here
}

?>
