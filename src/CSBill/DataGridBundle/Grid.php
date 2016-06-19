<?php

/*
 * This file is part of CSBill project.
 *
 * (c) 2013-2016 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CSBill\DataGridBundle;

use CSBill\DataGridBundle\Filter\FilterInterface;
use CSBill\DataGridBundle\Source\Source;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Serializer\ExclusionPolicy("ALL")
 */
class Grid implements GridInterface
{
    /**
     * @var string
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var ArrayCollection
     * @Serializer\Expose()
     */
    private $columns;

    /**
     * @var Source
     * @Serializer\Exclude()
     */
    private $source;

    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @var array
     * @Serializer\Expose()
     */
    private $actions;

    /**
     * @var array
     * @Serializer\Expose()
     */
    private $lineActions;

    /**
     * @var array
     * @Serializer\Expose()
     */
    private $properties;

    /**
     * @var string
     * @Serializer\Expose()
     */
    private $icon;

    /**
     * @var string
     * @Serializer\Expose()
     */
    private $title;

    /**
     * @var array
     * @Serializer\Expose()
     */
    private $parameters = [];

    /**
     * @param Source          $source
     * @param FilterInterface $filter
     * @param array           $gridData
     */
    public function __construct(Source $source, FilterInterface $filter, array $gridData)
    {
        $this->title = $gridData['title'];
        $this->name = $gridData['name'];
        $this->columns = new ArrayCollection(array_values($gridData['columns']));
        $this->source = $source;
        $this->actions = $gridData['actions'];
        $this->lineActions = $gridData['line_actions'];
        $this->properties = $gridData['properties'];
        $this->icon = $gridData['icon'];
        $this->filter = $filter;
    }

    /**
     * @param Request                $request
     * @param EntityManagerInterface $entityManager
     *
     * @return array
     *
     * @throws \Exception
     */
    public function fetchData(Request $request, EntityManagerInterface $entityManager)
    {
        $queryBuilder = $this->source->fetch($this->parameters);

        $this->filter->filter($request, $queryBuilder);

        $paginator = new Paginator($queryBuilder);

        return [
            'count' => count($paginator),
            'items' => $paginator->getQuery()->getArrayResult(),
        ];
    }

    /**
     * @return bool
     */
    public function requiresStatus()
    {
        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->contains('cell', 'status'));

        return count($this->columns->matching($criteria)) > 0;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param array $params
     */
    public function setParameters(array $params)
    {
        $this->parameters = $params;
    }
}
