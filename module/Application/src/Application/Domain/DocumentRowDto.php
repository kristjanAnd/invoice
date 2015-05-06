<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 6.05.15
 * Time: 17:40
 */

namespace Application\Domain;


use Application\Entity\Article;
use Application\Entity\Unit;
use Application\Entity\Vat;

class DocumentRowDto {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Application\Entity\Article
     */
    protected $article;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var \Application\Entity\Unit
     */
    protected $unit;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var \Application\Entity\Vat
     */
    protected $vat;

    /**
     * @var float
     */
    protected $vatAmount;

    /**
     * @var float
     */
    protected $amountVat;

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getAmountVat()
    {
        return $this->amountVat;
    }

    /**
     * @param mixed $amountVat
     */
    public function setAmountVat($amountVat)
    {
        $this->amountVat = $amountVat;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     */
    public function setArticle(Article $article = null)
    {
        $this->article = $article;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param Unit $unit
     */
    public function setUnit(Unit $unit = null)
    {
        $this->unit = $unit;
    }

    /**
     * @return Vat
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param Vat $vat
     */
    public function setVat(Vat $vat = null)
    {
        $this->vat = $vat;
    }

    /**
     * @return mixed
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * @param mixed $vatAmount
     */
    public function setVatAmount($vatAmount)
    {
        $this->vatAmount = $vatAmount;
    }


} 