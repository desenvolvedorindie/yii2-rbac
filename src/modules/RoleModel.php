<?php


namespace desenvolvedorindie\rbac\modules;

use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\rbac\Item;
use yii\rbac\Rule;

class RoleModel extends Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $type;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $ruleName;

    /**
     * @var string|null
     */
    public $data;

    /**
     * @var \yii\rbac\ManagerInterface
     */
    private $manager;

    /**
     * @var Item
     */
    private $_item;

    public function __construct($item = null, $config = [])
    {
        $this->_item = $item;
        $this->manager = Yii::$app->authManager;

        if ($item !== null) {
            $this->name = $item->name;
            $this->type = Item::TYPE_ROLE;
            $this->description = $item->description;
            $this->ruleName = $item->ruleName;
            $this->data = $item->data === null ? null : Json::encode($item->data);
        }

        parent::__construct($config);
    }

    public function validaName()
    {
        $value = $this->name;
        if ($this->manager->getRole($value) !== null) {
            $message = Yii::t('yii', '{attribute} "{value}" já foi criado.');
            $params = [
                'attribute' => $this->getAttributeLabel('name'),
                'value' => $value,
            ];
        }
        $this->addError('name', Yii::$app->getI18n()->format($message, $params, Yii::$app->language));
    }


    public function checkRule()
    {
        $name = $this->ruleName;

        if (!$this->manager->getRule($name)) {
            try {
                $rule = Yii::createObject($name);
                if ($rule instanceof Rule) {
                    $rule->name = $name;
                    $this->manager->add($rule);
                } else {
                    $this->addError('ruleName', Yii::t('yii2mod.rbac', 'Invalid rule "{value}"', ['value' => $name]));
                }
            } catch (Exception $exc) {
                $this->addError('ruleName', Yii::t('yii2mod.rbac', 'Rule "{value}" does not exists', ['value' => $name]));
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('yii2-rbac', 'Nome'),
            'type' => Yii::t('yii2-rbac', 'Tipo'),
            'description' => Yii::t('yii2-rbac', 'Descrição'),
            'ruleName' => Yii::t('yii2-rbac', 'Regra'),
            'data' => Yii::t('yii2-rbac', 'Dados'),
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'description', 'data', 'ruleName'], 'trim'],
            [['name', 'type'], 'required'],
            ['ruleName', 'checkRule'],
            ['name', 'validateName', 'when' => function () {
                return $this->getIsNewRecord() || ($this->_item->name != $this->name);
            }],
            ['type', 'integer'],
            [['description', 'data', 'ruleName'], 'default'],
            ['name', 'string', 'max' => 64],
        ];
    }

    /**
     * Check if is new record.
     *
     * @return bool
     */
    public function getIsNewRecord()
    {
        return $this->_item === null;
    }

    /**
     * @param string $id
     * @return RoleModel|null
     */
    public static function find(string $id)
    {
        $item = Yii::$app->authManager->getRole($id);

        if ($item !== null) {
            return new self($item);
        }

        return null;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function save()
    {
        $isNew = false;
        $oldName = null;

        if ($this->validate()) {
            if($this->getIsNewRecord()){
                if ($this->type == Item::TYPE_ROLE) {
                    $this->_item = $this->manager->createRole($this->name);
                } else {
                    $message = Yii::t('yii2-rbac', '"{value}" não é uma Role');
                    $params = [
                        'value' => $this->name
                    ];
                    $this->addError('type', Yii::$app->getI18n()->format($message,$params,Yii::$app->language));

                    return false;
                }

                $isNew = true;
            }

            $this->_item->name = $this->name;
            $this->_item->description = $this->description;
            $this->_item->ruleName = $this->ruleName;
            $this->_item->data = Json::decode($this->data);

            if ($isNew) {
                $this->manager->add($this->_item);
            } else {
                $this->manager->update($oldName, $this->_item);
            }

            return true;
        }
        return false;
    }

    /**
     * @param array $items
     * @return bool
     * @throws \yii\base\Exception
     */
    public function addChildren(array $items)
    {
        if ($this->_item) {
            foreach ($items as $name) {
                    $child = $this->manager->getRole($name);
                $this->manager->addChild($this->_item, $child);
            }
        }

        return true;
    }

    /**
     * @param array $items
     * @return bool
     */
    public function removeChildren(array $items)
    {
        if ($this->_item !== null) {
            foreach ($items as $name) {
                $child = $this->manager->getRole($name);
                $this->manager->removeChild($this->_item, $child);
            }
        }

        return true;
    }
}