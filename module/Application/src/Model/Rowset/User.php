<?php
namespace Application\Model\Rowset;

use Laminas\Filter\ToInt;

class User extends AbstractModel implements \Laminas\InputFilter\InputFilterAwareInterface
{
    public $inputFilter = null;

    public $name = null;

    public $email = null;

    public $password = null;

    public $id = null;

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = $value;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    public function exchangeArray(array $row)
    {
        $this->id = (!empty($row['id'])) ? $row['id'] : null;
        $this->name = (!empty($row['name'])) ? $row['name'] : null;
        $this->email = (!empty($row['email'])) ? $row['email'] : null;
        $this->password = (!empty($row['password'])) ? $row['password'] : null;
        $this->id = (!empty($row['id'])) ? $row['id'] : null;
    }

    public function getArrayCopy()
    {
        return[
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'id' => $this->getId(),
        ];
    }

    public function getInputFilter(bool $includeIdField = true)
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new \Laminas\InputFilter\InputFilter();

        if ($includeIdField) {
            $inputFilter->add([
                'name' => 'id',
                'required' => true,
                'filters' => [
                    ['name' => ToInt::class],
                ],
            ]);
        }
        $inputFilter->add([
            'name' => 'name',
        ]);

        $inputFilter->add([
            'name' => 'email',
        ]);

        $inputFilter->add([
            'name' => 'password',
        ]);


        $this->inputFilter = $inputFilter;
        return $inputFilter;
    }

    public function setInputFilter(\Laminas\InputFilter\InputFilterInterface $inputFilter)
    {
        throw new DomainException('This class does not support adding of extra input filters');
    }
}
