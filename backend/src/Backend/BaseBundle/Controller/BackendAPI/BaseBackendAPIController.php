<?php
namespace Backend\BaseBundle\Controller\BackendAPI;

use Backend\BaseBundle\Controller\API\BaseController;
use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Backend\BaseBundle\Form\Type\BaseFormType;
use Backend\BaseBundle\Model\OperationLogPeer;
use Backend\BaseBundle\Model\SiteConfigQuery;
use Backend\BaseBundle\Propel\I18n;
use DateTime;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Widget\CategoryArticleBundle\Model\CArticleI18nPeer;
use Widget\OrderBundle\Model\OrderQuery;

abstract class BaseBackendAPIController extends BaseController
{
    const LIST_ROWS_PER_PAGE = 10;
    /**
     * @return APIFormTypeItem[]
     */
    abstract protected function getFormConfig();

    protected function bindObject(\BaseObject $object, $jsonData)
    {
        return $this->bindObjectWithRawData($object, json_decode($jsonData, true));
    }

    protected function filterExtraColumn($formData)
    {
        $filteredData = array();
        $formConfig = $this->getFormConfig();
        foreach ($formConfig as $config){
            if(isset($formData[$config->getFieldName()])){
                $filteredData[$config->getFieldName()] = $formData[$config->getFieldName()];
            }
        }
        return $filteredData;
    }

    protected function createFormErrorJsonResponse(FormErrorIterator $errors, $headers = array())
    {
        return $this->createJsonSerializeResponse($errors, array(), Response::HTTP_BAD_REQUEST, $headers);
    }

    protected function validateSearchCondition($parameters)
    {
        if (isset($parameters['search']) && !is_array($parameters['search'])) {
            return false;
        }
        if (isset($parameters['sort']) && !is_array($parameters['sort'])) {
            return false;
        }
        if (isset($parameters['page']) && !is_numeric($parameters['page'])) {
            return false;
        }
        return true;
    }

    /**
     * @param \ModelCriteria $query
     * @return \ModelCriteria
     */
    protected function applySearchQuery(\ModelCriteria $query, $peerClass, $parameters)
    {
        $parameters = array_merge(array(
            'search' => array(),
            'sort' => array(),
            'page' => 1,
        ), $parameters);

        foreach($parameters['search'] as $column => $value){
            if ($query instanceof I18n){
                $this->applyJoinI18Condition($query, $peerClass, $column, $value, function($query, $peerClass, $column, $value){
                    $this->applySearchCondition($query, $peerClass, $column, $value);
                });
            }else {
                $this->applyJoinCondition($query, $peerClass, $column, $value, function ($query, $peerClass, $column, $value) {
                    $this->applySearchCondition($query, $peerClass, $column, $value);
                });
            }
        }

        foreach($parameters['sort'] as $column => $order){
            if ($query instanceof I18n){
                $this->applyJoinI18Condition($query, $peerClass, $column, $order, function($query, $peerClass, $column, $order){
                    $this->applyOrderCondition($query, $peerClass, $column, $order);
                });
            }else {
                $this->applyJoinCondition($query, $peerClass, $column, $order, function ($query, $peerClass, $column, $order) {
                    $this->applyOrderCondition($query, $peerClass, $column, $order);
                });
            }
        }
        return $query;
    }

    protected function applyJoinCondition(\ModelCriteria $query, $peerClass, $joinColumns, $value, $callback)
    {
        $column = $joinColumns;
        $useJoins = explode('.', $joinColumns);

        if($useJoins) {
            $column = array_pop($useJoins);
        }

        foreach($useJoins as $join){
            $service = $this->get('case_convert')->convert($join);
            $method = "use{$service}Query";
            $query = $query->$method();
        }

        $callback($query, $peerClass, $column, $value);

        foreach ($useJoins as $join){
            $query = $query->endUse();
        }
    }

    protected function applyJoinI18Condition(\ModelCriteria $query, $peerClass, $joinColumns, $value, $callback)
    {
        $columnPeerClass = "{$query->getModelName()}I18nPeer";
        $column = $joinColumns;
        $phpColumnName = $columnPeerClass::translateFieldName($column, \BasePeer::TYPE_FIELDNAME, \BasePeer::TYPE_PHPNAME);
        $useJoins = explode('.', $joinColumns);

        if($useJoins) {
            $column = array_pop($useJoins);
        }

        foreach ($columnPeerClass::getFieldNames() as $val) {
            if ($phpColumnName === $val){
                $useJoins = array($columnPeerClass::TABLE_NAME);
            }
        }

        foreach($useJoins as $join){
            $service = $this->get('case_convert')->convert($join);
            $method = "use{$service}Query";
            $query = $query->$method();
        }

        $callback($query, $columnPeerClass, $column, $value);

        foreach ($useJoins as $join){
            $query = $query->endUse();
        }
    }

    protected function applyOrderCondition(\ModelCriteria $query, $peerClass, $column, $order)
    {
        if ($query instanceof I18n){
            $columnPeerClass = "{$query->getModelName()}I18nPeer";
        }
        else{
            $columnPeerClass = $query->getModelPeerName();
        }
        $phpColumnName = $columnPeerClass::translateFieldName($column, \BasePeer::TYPE_FIELDNAME, \BasePeer::TYPE_PHPNAME);
        $query->orderBy($phpColumnName, $order);
    }

    protected function applySearchCondition(\ModelCriteria $query, $peerClass, $column, $value)
    {
        if ($query instanceof I18n){
            $columnPeerClass = "{$query->getModelName()}I18nPeer";
        }
        else{
            $columnPeerClass = $query->getModelPeerName();
        }
        $phpColumnName = $columnPeerClass::translateFieldName($column, \BasePeer::TYPE_FIELDNAME, \BasePeer::TYPE_PHPNAME);
        $method = "filterBy{$phpColumnName}";
        $searchType = $this->checkSearchType($value);
        $this->applySearchField($searchType, $value, $query, $method);
    }

    protected function doSearch($parameters, \ModelCriteria $query, $peerClass)
    {
        $parameters = array_merge(array(
            'search' => array(),
            'page' => 1,
            'sort' => array(),
        ), $parameters);

        if(!$this->validateSearchCondition($parameters)){
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
        try {
            $query = $this->applySearchQuery($query, $peerClass, $parameters);

            return $this->createJsonSerializeResponse($query->paginate($parameters['page'], self::LIST_ROWS_PER_PAGE), array('list'));
        } catch (\Exception $e){
            return $this->createHttpExceptionResponse(Response::HTTP_BAD_REQUEST);
        }

    }

    protected function logOperation(\BaseObject $object)
    {
        $modify = array();
        foreach($object->getModifiedColumns() as $column){
            list($tableName, $columnName) = explode('.', $column);
            $modify[$columnName] = $object->getByName($columnName, \BasePeer::TYPE_FIELDNAME);

        }
        $peer = $object->getPeer();
        $tableName = $peer::TABLE_NAME;
        $this->get('backend.base_bundle.operationlog')->log(
            $this->getUser(),
            $object->isNew()?OperationLogPeer::MODIFY_TYPE_NEW:($object->isDeleted()?OperationLogPeer::MODIFY_TYPE_DELETE:OperationLogPeer::MODIFY_TYPE_UPDATE),
            $tableName,
            $object->isDeleted()?$object->toArray(\BasePeer::TYPE_FIELDNAME):$modify
        );
    }

    protected function doProcessForm(\BaseObject $object, $content)
    {
        $form = $this->bindObject($object, $content);

        if(!($form->isValid())) {
            return $this->createFormErrorJsonResponse($form->getErrors(true));
        }

        $this->logOperation($object);
        $this->updateObject($object);

        return $this->createJsonSerializeResponse($object, array(
            'detail'
        ));
    }

    protected function updateObject(\BaseObject $object)
    {
        $object->save();
    }

    protected function deleteObject(\BaseObject $object)
    {
        $object->delete();
        $this->logOperation($object);
    }

    public function batchAction(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['ids']) || !is_array($data['ids'])) {
                return $this->createHttpExceptionResponse(Response::HTTP_BAD_REQUEST);
            }

            if (!isset($data['action'])) {
                return $this->createHttpExceptionResponse(Response::HTTP_BAD_REQUEST);
            }

            $con = \Propel::getConnection();
            $con->beginTransaction();

            switch ($data['action']) {
                case 'switch':
                    if (!isset($data['column'])) {
                        return $this->createHttpExceptionResponse(Response::HTTP_BAD_REQUEST);
                    }
                    $this->batchSwitch($data['ids'], $data['column'], $con);
                    break;
                case 'value':
                    if (!isset($data['column'])) {
                        return $this->createHttpExceptionResponse(Response::HTTP_BAD_REQUEST);
                    }
                    if (!isset($data['value'])) {
                        return $this->createHttpExceptionResponse(Response::HTTP_BAD_REQUEST);
                    }
                    $this->batchValue($data['ids'], $data['column'], $data['value'], $con);
                    break;
                case 'delete':
                    $this->batchDelete($data['ids'], $con);
                    break;
                default:
                    return $this->createHttpExceptionResponse(Response::HTTP_NOT_ACCEPTABLE, print_r($data, true));
            }
            $con->commit();
        } catch (\PropelException $e){
            return $this->createHttpExceptionResponse(Response::HTTP_EXPECTATION_FAILED, $e->getMessage());
        }
        return $this->createJsonResponse();
    }

    protected function batchSwitch($ids, $column, \PropelPDO $con)
    {
        return $this->createHttpExceptionResponse(Response::HTTP_NOT_ACCEPTABLE);
    }

    protected function batchValue($ids, $column, $value, \PropelPDO $con)
    {
        return $this->createHttpExceptionResponse(Response::HTTP_NOT_ACCEPTABLE);
    }

    protected function batchDelete($ids, \PropelPDO $con)
    {
        return $this->createHttpExceptionResponse(Response::HTTP_NOT_ACCEPTABLE);
    }

    protected function readSiteConfig($configName) {
        $siteConfig = SiteConfigQuery::create()->filterByName($configName)->findOneOrCreate();
        return $this->createJsonResponse($siteConfig->toArray()['Config']);
    }

    protected function updateSiteConfig($configName, $content) {
        try {
            $siteConfig = SiteConfigQuery::create()->filterByName($configName)->findOneOrCreate();
            $siteConfig->setConfig(json_decode($content, true));
            //return $this->createJsonSerializeResponse(array('Shipment.shipment' => 'ggrunrun'), array(), Response::HTTP_BAD_REQUEST);
            $siteConfig->save();
            return $this->createJsonResponse(['id' => $configName]);
        }
        catch (\PropelException $e) {
            return $this->createHttpExceptionResponse(Response::HTTP_BAD_REQUEST);
        }
    }

    protected function siteConfigValidator($content)
    {

    }

    /**
     * @param $value
     * @return string
     */
    private function checkSearchType($value)
    {
        $valeArray = explode(".", $value);
        if (isset($valeArray[0]) && isset($valeArray[1]) && DateTime::createFromFormat("Y-m-d", $valeArray[0]) !== false && DateTime::createFromFormat("Y-m-d", $valeArray[1]) !== false) {
            return "date";
        } elseif (isset($valeArray[0]) && isset($valeArray[1]) && DateTime::createFromFormat("Y-m-d H:i:s", $valeArray[0]) !== false && DateTime::createFromFormat("Y-m-d H:i:s", $valeArray[1]) !== false) {
            return "dateTime";
        }
        return "string";
    }

    /**
     * 套用 propel 對應的欄位搜尋格式
     * @param $searchType
     * @param $value
     * @param $method
     */
    private function applySearchField($searchType, $value,\ModelCriteria $query, $method)
    {
        $valeArray = explode(".", $value);
        switch ($searchType){
            case "date":
                $date = $this->getDateFormat($valeArray);
                $query->$method(array('min' => $date['min'], 'max' => $date['max']));
                //$query->$method(array('min' => $valeArray[0], 'max' => $valeArray[1]));
                break;
            case "dateTime":
                $date = $this->getDateTimeFormat($valeArray);
                $query->$method(array('min' => $date['min'], 'max' => $date['max']));
                //$query->$method(array('min' => $valeArray[0], 'max' => $valeArray[1]));
                break;
            default:
                $like = null;
                if(strpos($value, '%') !== false){
                    $like = \Criteria::LIKE;
                }
                $query->$method($value, $like);
                break;
        }
    }

    /**
     * Date type 回傳組好大小順序的 array
     * @param $valeArray
     * @return  array
     */
    private function getDateFormat(Array $valeArray)
    {
        $dateArray = $this->sortByDate($valeArray);
        return array(
            'min' => $dateArray[0] . ' 00:00:00',
            'max' => $dateArray[1] . ' 23:59:59'
        );
    }

    /**
     * DateTime type 回傳組好大小順序的 array
     * @param $valeArray
     * @return  array
     */
    private function getDateTimeFormat(Array $valeArray)
    {
        $dateArray = $this->sortByDate($valeArray);
        return array(
            'min' => $dateArray[0],
            'max' => $dateArray[1]
        );
    }

    private function sortByDate(Array $dateArray)
    {
        usort($dateArray, function($a, $b){
            return strtotime($a) - strtotime($b);
        });
        return $dateArray;
    }

    /**
     * @param \BaseObject $object
     * @param $rawData
     * @return \Symfony\Component\Form\Form
     */
    protected function bindObjectWithRawData(\BaseObject $object, $rawData)
    {
        $formData = $this->filterExtraColumn($rawData);
        $formConfig = $this->getFormConfig();
        $form = $this->createForm(
            new BaseFormType(function(FormBuilderInterface $builder, array $options) use($formConfig){
                foreach ($formConfig as $config){
                    $builder
                        ->add($config->getFieldName(), $config->getFieldType(), $config->getOptions());
                }
            }, 'form'),
            $object,
            array('csrf_protection' => false)
        );
        $form->submit($formData, false);
        return $form;
    }

}