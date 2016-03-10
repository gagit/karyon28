<?php

namespace BDS\DoctrineBundle\lib\Doctrine\DBAL\Event\Listeners;

use Doctrine\DBAL\Event\Listeners\OracleSessionInit as BaseOracleSessionInit;
use Doctrine\DBAL\Event\ConnectionEventArgs;

/**
 * Esta clase se usará cuando el entorno de Oracle no cumpla con los requerimientos de Doctrine.
 *
 * Las siguientes varialbes de entorno son requeridas para el formato de fecha por defecto en Doctrine
 * NLS_TIME_FORMAT="HH24:MI:SS"
 * NLS_DATE_FORMAT="YYYY-MM-DD HH24:MI:SS"
 * NLS_TIMESTAMP_FORMAT="YYYY-MM-DD HH24:MI:SS"
 * NLS_TIMESTAMP_TZ_FORMAT="YYYY-MM-DD HH24:MI:SS TZH:TZM"
 *
 * @author      Gabriel Toledo
 */
class OracleSessionInit extends BaseOracleSessionInit
{

    /**
     * @param ConnectionEventArgs $args
     * @return void
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        if (count($this->_defaultSessionVars) && 
                $args->getConnection()->getDriver()->getName()==='oci8') {
            array_change_key_case($this->_defaultSessionVars, \CASE_UPPER);
            $vars = array();
            foreach ($this->_defaultSessionVars as $option => $value) {
                $vars[] = $option." = '".$value."'";
            }
            $sql = "ALTER SESSION SET ".implode(" ", $vars);
            $args->getConnection()->executeUpdate($sql);
        }
    }

}
