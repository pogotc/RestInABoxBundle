<?php

namespace Pogotc\RestInABoxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class APIController extends Controller
{
	
    public function createAction($entity)
    {
		$request = $this->getRequest();
		
		//Refactor this...
		$fullClassName = $this->findEntityClassName($entity);
		if(!class_exists($fullClassName)){

			throw new \Exception("Could not find entity ".$entity);
		}

		$object = new $fullClassName();
		foreach($this->getUpdateableFields($entity) as $updateableField){
			foreach($request->request->all() as $key=>$val){
				if($updateableField == $key){
					$object->$key = $val;
				}
			}
		}
		
		$em = $this->getDoctrine()->getEntityManager();
		$em->persist($object);
		$em->flush();

		return $this->render('PogotcRestInABoxBundle:Default:response.json.twig', array(
			'json' => json_encode(array(
				'id' => $object->getId()
			))
		));
    }

	public function allAction($entity){
		$em = $this->getDoctrine()->getEntityManager();
		$allObjects = $em->getRepository($this->findEntityBundleName($entity))
					->findAll();
	
		$jsonResult = array();
		
		$listableFields = $this->getDisplayableFields($entity);
		if(count($allObjects)){
			foreach($allObjects as $object){
				$result = array();
				foreach($listableFields as $listableField){
					$result[$listableField] = $object->$listableField;
				}
				$jsonResult[]= $result;
			}
		}
		return $this->render('PogotcRestInABoxBundle:Default:response.json.twig', array(
			'json' => json_encode($jsonResult)
		));
	}
	
	public function viewAction($entity, $id){
		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->getRequest();
		$object = $em->getRepository($this->findEntityBundleName($entity))
					->findOneById($id);

		if(!$object){
			throw new \Exception("Invalid id, could not load object ".$entity);
		}
		
		$jsonResponse = array();
		foreach($this->getDisplayableFields($entity) as $listableField){
			$jsonResponse[$listableField] = $object->$listableField;
		}
		
		return $this->render('PogotcRestInABoxBundle:Default:response.json.twig', array(
			'json' => json_encode($jsonResponse)
		));
	}
	
	public function updateAction($entity, $id){
		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->getRequest();
		$object = $em->getRepository($this->findEntityBundleName($entity))
					->findOneById($id);

		if(!$object){
			throw new \Exception("Invalid id, could not load object ".$entity);
		}
		
		foreach($this->getUpdateableFields($entity) as $updateableField){
			foreach($request->request->all() as $key=>$val){
				if($updateableField == $key){
					$object->$key = $val;
				}
			}
		}
		$em->flush();
		
		$jsonResponse = array();
		foreach($this->getDisplayableFields($entity) as $listableField){
			$jsonResponse[$listableField] = $object->$listableField;
		}
		
		return $this->render('PogotcRestInABoxBundle:Default:response.json.twig', array(
			'json' => json_encode($jsonResponse)
		));
	}
	
	public function deleteAction($entity, $id){
		$em = $this->getDoctrine()->getEntityManager();
		$request = $this->getRequest();
		$object = $em->getRepository($this->findEntityBundleName($entity))
					->findOneById($id);

		if(!$object){
			throw new \Exception("Invalid id, could not load object ".$entity);
		}
		
		$em->remove($object);
		$em->flush();
	}

	protected function findEntityClassName($entity){
		$restObjects = $this->container->getParameter("pogotc_rest_in_a_box.rest_objects");
		if(array_key_exists($entity, $restObjects) && array_key_exists("class", $restObjects[$entity])){
			return '\\'.$restObjects[$entity]['class'];
		}else{
			throw new \Exception("No REST config specified for type ".$entity);
		}
	}
	
	protected function findEntityBundleName($entity){
		return 'PogotcAPIBundle:User';
	}
	
	protected function getUpdateableFields($entity){
		$restObjects = $this->container->getParameter("pogotc_rest_in_a_box.rest_objects");
		if(array_key_exists($entity, $restObjects)){
			if(array_key_exists("updateableFields", $restObjects[$entity])){
				return $restObjects[$entity]['updateableFields'];
			}else{
				return array();
			}
			
		}else{
			throw new \Exception("No REST config specified for type ".$entity);
		}
	}
	
	protected function getDisplayableFields($entity){
		$restObjects = $this->container->getParameter("pogotc_rest_in_a_box.rest_objects");
		if(array_key_exists($entity, $restObjects)){
			if(array_key_exists("displayableFields", $restObjects[$entity])){
				return $restObjects[$entity]['displayableFields'];
			}else{
				return array();
			}
			
		}else{
			throw new \Exception("No REST config specified for type ".$entity);
		}
	}
}
