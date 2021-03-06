<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 17/12/15
 * Time: 00:15
 */

namespace BitLogic\BaseBundle\Grilla;

/*
filterscount:0
groupscount:0
pagenum:1
pagesize:10
recordstartindex:10
recordendindex:20
*/

/**
 * Class Grilla
 *
 * @package BitLogic\BaseBundle\Grilla
 */
class GrillaHtml
{
    /**
     * @var number
     */
    private $pagenum;

    /**
     * @var number
     */
    private $pagesize;

    /**
     * @var string
     */
    private $sortdatafield;

    /**
     * @var string
     */
    private $sortorder;

    /**
     * @var array
     */
    private $data;

    /** @var  array */
    private $filter_fields;

    public function __construct()
    {
        $this->pagenum = 0;
        $this->pagesize = 10;
        $this->filter_fields = [];
    }

    /**
     * @return number
     */
    public function getPagenum()
    {
        return $this->pagenum;
    }

    /**
     * @param number $pagenum
     * @return Grilla
     */
    public function setPagenum($pagenum)
    {
        $this->pagenum = $pagenum;
        return $this;
    }

    /**
     * @return number
     */
    public function getPagesize()
    {
        return $this->pagesize;
    }

    /**
     * @param number $pagesize
     * @return Grilla
     */
    public function setPagesize($pagesize)
    {
        $this->pagesize = $pagesize;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortdatafield()
    {
        return $this->sortdatafield;
    }

    /**
     * @param string $sortdatafield
     * @return Grilla
     */
    public function setSortdatafield($sortdatafield)
    {
        $this->sortdatafield = $sortdatafield;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortorder()
    {
        return $this->sortorder;
    }

    /**
     * @param string $sortorder
     * @return Grilla
     */
    public function setSortorder($sortorder)
    {
        $this->sortorder = $sortorder;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return Grilla
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getFilterFields()
    {
        return $this->filter_fields;
    }

    /**
     * @param array $filter_fields
     * @return Grilla
     */
    public function setFilterFields($filter_fields)
    {
        $this->filter_fields = $filter_fields;
        return $this;
    }

    public function getFirstResult()
    {
        return ($this->pagenum+1) * $this->pagesize;
    }

    public function __set($name, $value)
    {
        $this->filter_fields[$name] = $value;

        return $this;
    }

    public function __get($name)
    {
        if(array_key_exists($name, $this->filter_fields)){
            return $this->filter_fields[$name];
        } else {
            return false;
        }
    }
}