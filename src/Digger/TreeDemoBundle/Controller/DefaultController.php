<?php

namespace Digger\TreeDemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Digger\TreeDemoBundle\Entity\Category;
use Digger\TreeDemoBundle\Form\CategoryType;


class DefaultController extends Controller
{
    /**
     * @Route("/edit/{id}, name='digger_tree_demo_edit', requirements={'id' = '\d+;'}, defaults={'id' = 0}")
     * @Template()
     */
    public function editAction($id = 0)
    {
        $request  = $this->getRequest();

        // just setup a fresh $category object
        if (!$id) {
            $category = new Category();
        } else {
            $em       = $this->getDoctrine()->getManager();
            $repo     = $em->getRepository('Digger\TreeDemoBundle\Entity\Category');
            $category = $repo->find($id);
        }

        $form     = $this->createForm(new CategoryType(), $category);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // perform some action, such as saving the task to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();
                return $this->redirect($this->generateUrl('index'));
            }
        }
        return array('form' => $form->createView(), 'id' => $id);
    }
    
    /**
     * @Route("/",     name="index_empty", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Route("/{id}", name="index")
     * @Template()
     */
    public function indexAction($id = 0)
    {
        $em    = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Digger\TreeDemoBundle\Entity\Category');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul class="tree">',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return '<a href="/'.$node['id'].'">'.$node['title'].'</a>';
                 
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
     * @Route("/delete/{id}")
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
            }
            
            return $this->redirect($this->generateUrl('index'));
    }
    
    
    /**
     * @Route("/fill, name=fill")
     * @Template()
     */
    public function fillAction()
    {
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


        return array('name' => 'test');
    }
}
