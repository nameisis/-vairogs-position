<?php

namespace Vairogs\Utils\Position\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class TreeController extends AbstractController
{
    public function move(Request $request, $class, $id, $position)
    {
        $map = $this->getParameter('vairogs.utils.position.entities') ?? [];
        if (empty($map)) {
            throw new InvalidConfigurationException('Please configure vairogs.utils.position.entities to use move functionality');
        }
        $entity = $map[$class];
        $repo = $this->getDoctrine()->getManager($entity['manager'])->getRepository(\array_keys($entity)[0]);
        $object = $repo->find($id);
        if ($object && $object->getParent()) {
            $repo->{'move'.\ucfirst($position)}($object);
        }

        return new RedirectResponse($request->headers->get('referer'), RedirectResponse::HTTP_FOUND);
    }
}
