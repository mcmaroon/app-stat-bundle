<?php

namespace App\StatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller {

    public function indexAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppStatBundle:Stat');
        $types = $repository->findTypes();

        $filters = $request->query->get('filters', array());

        if (!isset($filters['date_start'])) {
            $date = new \DateTime("now");
            $date->modify('-7 days');
            $filters['date_start'] = $date->format('Y-m-d');
        }

        return $this->render('AppStatBundle:Default:index.html.twig', array(
                    'filters' => $filters,
                    'types' => $types
        ));
    }

    // ~

    public function asyncAction(Request $request) {

        $translated = $this->get('translator');

        $renderData = array(
            'items' => array(),
            'groups' => array(),
            'count' => 0
        );

        $log = $this->get('app.log');

        $filters = (array) $request->query->get('filters', array());

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('AppStatBundle:Stat');

        $entities = $repository->findByFilters($filters);

        $headers = array('date' => 'stat.enum.headers.default');
        $items = array();

        foreach ($entities as $entity) {

            $objectType = $entity->getTypeGroup();

            $objectKey = $entity->getTypeGroupKey();

            $group = ($objectKey !== null && $objectType !== null) ? $objectKey : $entity->getType();

            $groupName = $translated->trans('stat.enum.' . $group);

            if ($objectKey !== null && $objectType !== null) {
                try {
                    $objectKeyRepository = $em->getRepository('AppMainBundle:' . $objectType);
                    $objectKeyItem = $objectKeyRepository->find($objectKey);
                    if ($objectKeyItem !== null) {
                        $groupName = (string) $objectKeyItem; // to string entity method
                    }
                } catch (\Exception $exc) {
                    $log->error('StatDefaultController', array(
                        'code' => $exc->getCode(),
                        'message' => $exc->getMessage()
                    ));
                }
            }

            $headers[$groupName] = $groupName;

            $date = $entity->getCreatedAt()->format('Y-m-d H:i');

            $items[$date]['date'] = $date;
            $items[$date][$groupName] = $entity->getValue();
        }

        $newItems = array();
        $newItems[] = array_values($headers);
        $tempHeaders = array_fill_keys(array_keys($headers), null);

        foreach ($items as $item) {
            $newItems[] = array_values(array_merge($tempHeaders, $item));
        }

        $renderData = array(
            'items' => $newItems,
            'count' => count($newItems)
        );

        // ~

        /* $log->debug('StatDefaultController:async', array(
          'filters' => $filters,
          'renderData' => $renderData,
          )); */

        return new JsonResponse($renderData);
    }

}
