<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\UserKey;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\User\Attribute;
use Symfony\Component\HttpFoundation\Request;

class UserCategory extends AbstractCategory implements StandardSearchIndexerInterface
{

    public function createAttributeKey()
    {
        return new UserKey();
    }

    public function getIndexedSearchTable()
    {
        return 'UserSearchIndexAttributes';
    }

    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getUserID();
    }


    public function getSearchIndexFieldDefinition()
    {
        return array(
            'columns' => array(
                array(
                    'name' => 'uID',
                    'type' => 'integer',
                    'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true)
                )
            ),
            'primary' => array('uID')
        );
    }

    public function getAttributeRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\UserKey');
    }

    public function getRegistrationList()
    {
        return $this->getAttributeRepository()->getRegistrationList();
    }

    /**
     * @param UserKey $key
     * @param Request $request
     * @return Key
     */
    protected function saveFromRequest(Key $key, Request $request)
    {
        $key->setAttributeKeyDisplayedOnProfile((string) $request->request->get('uakProfileDisplay') == 1);
        $key->setAttributeKeyEditableOnProfile((string) $request->request->get('uakProfileEdit') == 1);
        $key->setAttributeKeyRequiredOnProfile((string) $request->request->get('uakProfileEditRequired') == 1);
        $key->setAttributeKeyEditableOnRegister((string) $request->request->get('uakRegisterEdit') == 1);
        $key->setAttributeKeyRequiredOnRegister((string) $request->request->get('uakRegisterEditRequired') == 1);
        $key->setAttributeKeyDisplayedOnMemberList((string) $request->request->get('uakMemberListDisplay') == 1);
        return $key;
    }

    public function addFromRequest(Type $type, Request $request)
    {
        $key = parent::addFromRequest($type, $request);
        return $this->saveFromRequest($key, $request);
    }

    public function updateFromRequest(Key $key, Request $request)
    {
        $key = parent::updateFromRequest($key, $request);
        return $this->saveFromRequest($key, $request);
    }

    public function import(Type $type, \SimpleXMLElement $element)
    {
        /**
         * @var $key UserKey
         */
        $key = parent::import($type, $element);
        $key->setAttributeKeyDisplayedOnProfile((string) $element['profile-displayed'] == 1);
        $key->setAttributeKeyEditableOnProfile((string) $element['profile-editable'] == 1);
        $key->setAttributeKeyRequiredOnProfile((string) $element['profile-required'] == 1);
        $key->setAttributeKeyEditableOnRegister((string) $element['register-editable'] == 1);
        $key->setAttributeKeyRequiredOnRegister((string) $element['register-required'] == 1);
        $key->setAttributeKeyDisplayedOnMemberList((string) $element['member-list-displayed'] == 1);
        return $key;
    }

    public function getAttributeValues($user)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\User\AttributeValue');
        $values = $r->findBy(array(
            'uID' => $user->getUserID()
        ));
        return $values;
    }




}
