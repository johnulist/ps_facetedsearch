<?php
/**
 * 2007-2019 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\Module\FacetedSearch\Adapter;

use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractAdapter implements InterfaceAdapter
{
    /**
     * @var ArrayCollection
     */
    protected $filters;

    /**
     * @var ArrayCollection
     */
    protected $operationsFilters;

    protected $orderField = 'id_product';

    protected $orderDirection = 'DESC';

    protected $selectFields = [];

    protected $groupFields = [];

    protected $limit = 20;

    protected $offset = 0;

    /** @var InterfaceAdapter */
    protected $initialPopulation = null;

    public function __construct()
    {
        $this->filters = new ArrayCollection();
        $this->operationsFilters = new ArrayCollection();
    }

    public function __clone()
    {
        $this->filters = clone $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getInitialPopulation()
    {
        return $this->initialPopulation;
    }

    /**
     * {@inheritdoc}
     */
    public function resetFilter($filterName)
    {
        unset($this->filters[$filterName]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resetOperationsFilters()
    {
        $this->operationsFilters = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resetAll()
    {
        $this->selectFields = [];
        $this->groupFields = [];
        $this->filters = [];
        $this->operationsFilters = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter($filterName)
    {
        if (isset($this->filters[$filterName])) {
            return $this->filters[$filterName];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getOperationsFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFilters(InterfaceAdapter $facetedSearch)
    {
        $this->filters = $facetedSearch->getFilters();
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter($filterName, $values, $operator = '=')
    {
        $filters = $this->filters->get($filterName);
        if (!isset($filters[$operator])) {
            $filters[$operator] = [];
        }

        $filters[$operator][] = $values;
        $this->filters->set($filterName, $filters);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addOperationsFilter($filterName, array $operations = [])
    {
        $this->operationsFilters->set($filterName, $operations);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addSelectField($fieldName)
    {
        if (!in_array($fieldName, $this->selectFields)) {
            $this->selectFields[] = $fieldName;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSelectFields($selectFields)
    {
        $this->selectFields = $selectFields;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resetSelectField()
    {
        $this->selectFields = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addGroupBy($groupField)
    {
        $this->groupFields[] = $groupField;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroupFields($groupFields)
    {
        $this->groupFields = $groupFields;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resetGroupBy()
    {
        $this->groupFields = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter($filterName, $value)
    {
        if ($value !== null) {
            $this->filters[$filterName] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderField($fieldName)
    {
        $this->orderField = $fieldName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderDirection($direction)
    {
        $this->orderDirection = $direction === 'desc' ? 'desc' : 'asc';

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit, $offset = 0)
    {
        $this->limit = $limit ? (int) $limit : null;
        $this->offset = (int) $offset;

        return $this;
    }
}