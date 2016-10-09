<?php
//FUINCION QUE LISTA LAS OPCIONES DEL MENU PRINCIPAL SEGUN ESTEN HABILITADOS O NO EN LA SESSION
    function getColumnsDatatable($arrColumnos){
        $columnas = array();
        $defColums = array();
        $i = 0;
        foreach ($arrColumnos as $key=>$value) {
           $columnas[] = array("sName"=>$key);
           $visible = isset($value["visible"]) ? $value["visible"] : true;
           $seach = isset($value["seach"]) ? $value["seach"] : true;
           $sort = isset($value["sort"]) ? $value["sort"] : true;
           $class = isset($value["class"]) ? $value["class"] : "";
           $mRender = isset($value["mRender"]) ? $value["mRender"] : null;
           $sWidth = isset($value["sWidth"]) ? $value["sWidth"] : null;
           $bVisible = isset($value["bVisible"]) ? $value["bVisible"] : true;
           $defColums[] =  array(
               "sTitle"=>$value["nombre"],
               "sName"=>$key,
               "aTargets"=>array($i),
               "bVisible"=>$visible,
               "bSearchable" =>$seach,
               "bSortable" =>$sort,
               "sClass"=>$class,
               "mRender"=>$mRender,
               "sWidth"=>$sWidth,
               "bVisible"=>$bVisible
               );
           
           $i++;
           
        }
        return $defColums;
    }
