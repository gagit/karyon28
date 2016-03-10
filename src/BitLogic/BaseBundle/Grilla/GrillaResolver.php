<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 17/12/15
 * Time: 20:26
 */

namespace BitLogic\BaseBundle\Grilla;


use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class GrillaResolver
{
    public static function handleRequest($form, Request $request )
    {
        //$grilla = new Grilla();
        try{
            $form->handleRequest($request);
            /** @var Grilla $grilla */
            $grilla = $form->getData();
            if($grilla) {
                $grilla
                    ->setPagenum($request->get('pagenum'))
                    ->setPagesize($request->get('pagesize'))
                    ->setSortdatafield($request->get('sortdatafield'))
                    ->setSortorder($request->get('sortorder'));
                
            } else {
                $grilla = new Grilla();
            }
        }catch(\Exception $ex) {
            dump($ex);
        }
        return $grilla;
    }
}