<?php
function getlocalidades(){
           
           $idprovincia=$_POST['idprovincia'];
           
           if($idprovincia=='1'){
               $localidades=array(
                   array('id'=>1,'nombre'=>'localidad1'),
                   array('id'=>2,'nombre'=>'localidad2'),
                   array('id'=>3,'nombre'=>'localidad3')
               );
               echo json_encode($localidades);
           }else{
               $localidades=array(
                   array('id'=>4,'nombre'=>'localidad4'),
                   array('id'=>5,'nombre'=>'localidad5'),
                   array('id'=>6,'nombre'=>'localidad6')
                   
               );
               echo json_encode($localidades);
           }
           
       }

