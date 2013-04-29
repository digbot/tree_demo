<?php

namespace Digger\TreeDemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Digger\TreeDemoBundle\Entity\Category;
use Digger\TreeDemoBundle\Form\Type\CategoryType;


class DefaultController extends Controller
{
   /**
     * @Route("/add_root", name="add_root")
     * @Template()
     */
    public function addrouteAction()
    {      
        $this->truncateEntity("categories");
        
        $this->em    = $this->getDoctrine()->getManager();
        $root = new Category();
        $root->setTitle('Root');
        $this->em->persist($root);
        $this->em->flush();
        
        return $this->redirect($this->generateUrl('index'));
    }
    
    /**
     * @Route("/reload", name="reload")
     * @Template()
     */
    public function reloadAction()
    {      
        $this->truncateEntity("categories");
        
        $this->em    = $this->getDoctrine()->getManager();
        $food = new Category();
        $food->setTitle('Food');

        $fruits = new Category();
        $fruits->setTitle('Fruits');
        $fruits->setParent($food);

        $vegetables = new Category();
        $vegetables->setTitle('Vegetables');
        $vegetables->setParent($food);

        $carrots = new Category();
        $carrots->setTitle('Carrots');
        $carrots->setParent($vegetables);

        $this->em->persist($food);
        $this->em->persist($fruits);
        $this->em->persist($vegetables);
        $this->em->persist($carrots);
        $this->em->flush();
        
        return $this->redirect($this->generateUrl('index'));
    }
  
    /**
     * @Route("/",     name="index_empty", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Route("/{id}", name="index")
     * @Template()
     */
    public function indexAction($id = 0)
    {
        return array();
    }
    
    /**
     * @Route("/delete/{id}", name="delete")
     * @Template()
     */
    public function deleteAction($id = 0)
    {
        $em       = $this->getDoctrine()->getManager();
        $repo     = $em->getRepository('Digger\TreeDemoBundle\Entity\Category');
        $category = $repo->find($id);

        if ($category instanceof Category) {
            $em->remove($category);
            $em->flush();
            
            $this->get('session')->setFlash(
                'notice',
                'Category DELETED from tree successfully.'
            );
        }

        return $this->redirect($this->generateUrl('index'));
    }
    
    /**
     * @Route("/remove/{id}", name="remove")
     * @Template()
     */
    public function removeAction($id = 0)
    {
        $em       = $this->getDoctrine()->getManager();
        $repo     = $em->getRepository('Digger\TreeDemoBundle\Entity\Category');
        $category = $repo->find($id);

        if ($category instanceof Category) {
            $repo->removeFromTree($category);
            $em->clear();
            
            $this->get('session')->setFlash(
                'notice',
                'Category REMOVED from tree successfully.'
            );
        }

        return $this->redirect($this->generateUrl('index'));
    }
    
    private function truncateEntity($table)
    {
        $connection = $this->getDoctrine()->getManager()->getConnection();
        $platform   = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL($table, true /* whether to cascade */));
    }
}
