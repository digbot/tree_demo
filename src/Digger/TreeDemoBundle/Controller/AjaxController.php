<?php

namespace Digger\TreeDemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Digger\TreeDemoBundle\Entity\Category;
use Digger\TreeDemoBundle\Form\Type\CategoryType;
use Symfony\Component\HttpFoundation\Response;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax/",     name="ajax_index_empty", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Route("/ajax/{id}", name="ajax_index")
     * @Template("DiggerTreeDemoBundle:Ajax:index.html.twig")
     */
    public function indexAction($id = 0)
    {
        $id = 0;
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
                $colorCss = Category::getColorCssClass($node['color']);
                return '<a href="#node'.$node['id'].'"><span  class="'. $colorCss.'">'.$node['title'].'</span></a>';
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
     * @Route("/edit/{id}", name="ajax_edit", requirements={"id" = "\d+"}, defaults={"id" = 0})
     * @Template()
     */
    public function editAction($id = 0)
    { 
        $category = $this->findCategory($id);
        return $this->processForm($category, 'DiggerTreeDemoBundle:Ajax:edit.html.twig');     
    }
    
    /**
     * @Route("/add/{parentId}", name="ajax_add", requirements={"parentId" = "\d+"}, defaults={"parentId" = 1})
     * @Template()
     */
    public function addAction($parentId = 1)
    {
         $category = new Category();
         if ($parentId) {
            $parent   = $this->findCategory($parentId);
            $category->setParent($parent);
         }

        return $this->processForm($category, 'DiggerTreeDemoBundle:Ajax:add.html.twig');
    }    
    
     private function processForm(Category $category, $templating)
     { 
        $request  = $this->getRequest();
        $form     = $this->createForm(new CategoryType(), $category);
         
        if (is_null($category->getParent())) {
             $form->remove('parent');   
        }
                
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // perform some action, such as saving the task to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();
                
                $response = new Response('OK', 200);
                return $response;
            } else {
                $formHtml = $this->container->get('templating')->render($templating, array(
                        'form' => $form->createView()
                 ));

                $response  = new Response($formHtml, 422);
                $response->headers->set('Content-Type', 'text/html');
                return $response;
            }
        }
 
        return array(
            'form' => $form->createView()
        );
     }
    
    /**
     * @Route("/node/{id}", name="node", requirements={"id" = "\d+"}, defaults={"id" = 1})
     * @Template()
     */
    public function nodeAction($id = 1)
    {
        $request  = $this->getRequest();
        $path     = array();
        $em       = $this->getDoctrine()->getManager();
        $repo     = $em->getRepository('Digger\TreeDemoBundle\Entity\Category');
        $category = $repo->find($id);
        $path     = $repo->getPath($category);
           
        $parentId = NULL;
        
        if ($category->getParent() instanceof Category) {
                $parentId =  $category->getParent()->getId();
        }

        return array(
            'path'        => $path,
            'id'          => $id,
            'parentId'    => $parentId
        );
    }
    
    private function findCategory($id) 
    {
        $em       = $this->getDoctrine()->getManager();
        $repo     = $em->getRepository('Digger\TreeDemoBundle\Entity\Category');
        $category = $repo->find($id);
       
        return $category;
    }
}
