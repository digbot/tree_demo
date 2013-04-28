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
        $controller  = $this;
        $em          = $this->getDoctrine()->getManager();
        $repo        = $em->getRepository('Digger\TreeDemoBundle\Entity\Category');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul class="tree">',
            'rootClose' => '</ul>',
            'childOpen' =>  function($child) {
                 return '<li id="tree_node_'.$child['id'].'">';
             },
            'childClose' => '</li>',
            'nodeDecorator' => function($node) use (&$controller) {
                $url = $controller->generateUrl("index", array("id" => $node['id']));
                return '<a href="'.$url.'">'.$node['title'].'</a>';
            }
        );

        $htmlTree = $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* load all children, not only direct */
            $options
        );

        return array('htmlTree' => $htmlTree, 'id' => $id);
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
            //$repo->removeFromTree($category);
            $em->remove($category);
            $em->flush();
            $em->clear();
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
