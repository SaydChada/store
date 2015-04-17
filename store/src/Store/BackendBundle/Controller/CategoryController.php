<?php

namespace Store\BackendBundle\Controller;

use Store\BackendBundle\Entity\Category;
use Store\BackendBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * Module that handle product
 * @package Store\BackendBundle\Controller
 */
class CategoryController extends Controller
{
    /**
     * View list of categories
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        //Récupère toutes les catégories de ma base de données
        $categories = $em->getRepository('StoreBackendBundle:Category')->getCategoryByUser(1);
        return $this->render('StoreBackendBundle:Category:list.html.twig',['categories' => $categories]);
    }

    /**
     * View a category
     * @param $id
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id, $name)
    {
        $em = $this->getDoctrine()->getManager();
        //Récupère toutes les catégories de ma base de données
        $category = $em->getRepository('StoreBackendBundle:Category')->find($id);
        return $this->render('StoreBackendBundle:Category:view.html.twig',
            ['category' => $category]
        );
    }

    /**
     * Delete a category
     * @param $id
     * @internal param $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('StoreBackendBundle:Category')->find($id);
        //remove supprime l'objet en cache
        $em->remove($category);
        //Flush permet d'envoyer la requette en bdd pour faire persister la modification
        $em->flush();
        return $this->redirectToRoute('store_backend_category_list');
    }

    /**
     * Create a category
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @internal param $id
     * @internal param $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request){
        $category = new Category();

        //J'associe mon jeweler à ma catégorie
        $em = $this->getDoctrine()->getManager();
        $jeweler = $em->getRepository('StoreBackendBundle:Jeweler')->find(1);
        $category->setJeweler($jeweler);

        $form = $this->createForm(new CategoryType(), $category, [
            'validation_groups' => 'new',
            'attr' =>
                [
                    'method' => 'post',
                    'novalidate' => 'novalidate', //Permet de zaper la validation required html5
                    'action' => $this->generateUrl('store_backend_category_new')
                ]
        ]);

        //Envoie les donnés de la $request au formulaire, de tel sorte que le formulaire ai accès aux données
        $form->handleRequest($request);

        //Si la totalité de formulaire est valide
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('store_backend_category_list');
        }
        return $this->render('StoreBackendBundle:Category:new.html.twig',['form' => $form->createView()]);
    }
}
