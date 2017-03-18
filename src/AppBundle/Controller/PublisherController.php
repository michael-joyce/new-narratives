<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Publisher;
use AppBundle\Form\PublisherType;

/**
 * Publisher controller.
 *
 * @Route("/publisher")
 */
class PublisherController extends Controller
{
    /**
     * Lists all Publisher entities.
     *
     * @Route("/", name="publisher_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Publisher e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $publishers = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'publishers' => $publishers,
        );
    }
    /**
     * Search for Publisher entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Publisher repository. Replace the fieldName with
	 * something appropriate, and adjust the generated search.html.twig
	 * template.
	 * 
     //    public function searchQuery($q) {
     //        $qb = $this->createQueryBuilder('e');
     //        $qb->where("e.fieldName like '%$q%'");
     //        return $qb->getQuery();
     //    }
	 *
     *
     * @Route("/search", name="publisher_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Publisher');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$publishers = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$publishers = array();
		}

        return array(
            'publishers' => $publishers,
			'q' => $q,
        );
    }
    /**
     * Full text search for Publisher entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Publisher repository. Replace the fieldName with
	 * something appropriate, and adjust the generated fulltext.html.twig
	 * template.
	 * 
	//    public function fulltextQuery($q) {
	//        $qb = $this->createQueryBuilder('e');
	//        $qb->addSelect("MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') as score");
	//        $qb->add('where', "MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') > 0.5");
	//        $qb->orderBy('score', 'desc');
	//        $qb->setParameter('q', $q);
	//        return $qb->getQuery();
	//    }	 
	 * 
	 * Requires a MatchAgainst function be added to doctrine, and appropriate
	 * fulltext indexes on your Publisher entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="publisher_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Publisher');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$publishers = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$publishers = array();
		}

        return array(
            'publishers' => $publishers,
			'q' => $q,
        );
    }

    /**
     * Creates a new Publisher entity.
     *
     * @Route("/new", name="publisher_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        $publisher = new Publisher();
        $form = $this->createForm('AppBundle\Form\PublisherType', $publisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publisher);
            $em->flush();

            $this->addFlash('success', 'The new publisher was created.');
            return $this->redirectToRoute('publisher_show', array('id' => $publisher->getId()));
        }

        return array(
            'publisher' => $publisher,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Publisher entity.
     *
     * @Route("/{id}", name="publisher_show")
     * @Method("GET")
     * @Template()
	 * @param Publisher $publisher
     */
    public function showAction(Publisher $publisher)
    {

        return array(
            'publisher' => $publisher,
        );
    }

    /**
     * Displays a form to edit an existing Publisher entity.
     *
     * @Route("/{id}/edit", name="publisher_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param Publisher $publisher
     */
    public function editAction(Request $request, Publisher $publisher)
    {
        $editForm = $this->createForm('AppBundle\Form\PublisherType', $publisher);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The publisher has been updated.');
            return $this->redirectToRoute('publisher_show', array('id' => $publisher->getId()));
        }

        return array(
            'publisher' => $publisher,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Publisher entity.
     *
     * @Route("/{id}/delete", name="publisher_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param Publisher $publisher
     */
    public function deleteAction(Request $request, Publisher $publisher)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($publisher);
        $em->flush();
        $this->addFlash('success', 'The publisher was deleted.');

        return $this->redirectToRoute('publisher_index');
    }
}
